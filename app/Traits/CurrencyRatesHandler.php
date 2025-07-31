<?php

namespace App\Traits;

use App\Models\Cost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

trait CurrencyRatesHandler
{
    private array $allowdCurrencies = ['SYP', 'USD', 'EUR', 'TRY'];

    public function costs()
    {
        return $this->morphMany(Cost::class, 'costable');
    }

    protected static function booted(): void
    {
        try {
            static::created(function (Model $self) {
                defer(function () use ($self) {
                    $self->startRatesHandler();
                });
            });
        } catch (\Exception $e) {
            error_log(json_encode($e));
            throw $e;
        }
    }

    protected function startRatesHandler()
    {
        $this->checkIfPriceFieldExist();
        $result = $this->caculateRates();
        return $this->store($result);
    }

    protected function checkIfPriceFieldExist(): void
    {
        if (! Schema::hasColumn($this->getTable(), $this->getFieldToCalculateRatesFor())) {
            throw new \Exception(
                class_basename($this::class) . ' missing ' . $this->getFieldToCalculateRatesFor()
            );
        }
    }

    protected function getFieldToCalculateRatesFor(): string
    {
        return 'price';
    }

    protected function caculateRates(): array
    {
        $data = $this->getData();
        return $this->calculate($data);
    }

    protected function getData(): array
    {
        $todayFile = $this->getFilePath();
        if (File::exists($todayFile)) {
            $data = json_decode(File::get($todayFile), true);
            return $this->getRates($data['rates']);
        }
        return $this->getFromApi();
    }

    protected function getFromApi(): array
    {
        try {
            $responce = Http::get("https://open.er-api.com/v6/latest/{$this->getCurrency()}");
            if ($responce->successful()) {
                File::put(
                    $this->getFilePath(),
                    json_encode($responce->json(), JSON_PRETTY_PRINT)
                );
                return $this->getRates($responce->json('rates'));
            }
            return $this->readYesterdayFile();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getRates(array $rates): array
    {
        return array_intersect_key($rates, array_flip($this->allowdCurrencies));
    }

    public function calculate(?array $rates = null): array
    {
        $result = [];
        if ($rates === null) {
            return $result;
        }
        $cost = $this->getCost();
        foreach ($rates as $currency => $rate) {
            $is_main = $currency !== $this->getCurrency() ? false : true;
            $result[] = [
                'cost' => $cost,
                'from_currency' => $this->getCurrency(),
                'to_currency' => $currency,
                'rate' => $rate,
                'cost_after_change' => $cost * $rate,
                'is_main' => $is_main,
            ];
        }
        return $result;
    }

    protected function getCost(): int|string
    {
        return $this->{$this->getFieldToCalculateRatesFor()};
    }

    protected function getCurrency(): string
    {
        if (
            !request()->has('currency') ||
            !in_array(request()->input('currency'), $this->allowdCurrencies)
        ) {
            // throw new \Exception("currency is missing");
            return "SYP";
        }
        return request()->input('currency');
    }

    protected function store(array $data)
    {
        return $this->costs()->createMany($data);
    }

    protected function readYesterdayFile(): array
    {
        $yesterdayFile = $this->getFilePath(now()->yesterday());
        if (File::exists($yesterdayFile)) {
            $data = json_decode(File::get($yesterdayFile), true);
            return $this->getRates($data['rates']);
        }
        throw new \Exception('Error fetching currencies change rates');
    }

    protected function deleteYesterdayFile(): void
    {
        $yesterdayFile = $this->getFilePath(now()->yesterday());
        if (File::exists($yesterdayFile)) {
            File::delete($yesterdayFile);
        }
        throw new \Exception('Yesterday file is missing');
    }

    protected function getFilePath($date = null): string
    {
        if ($date === null) {
            $date = now();
        }
        if (!File::exists(storage_path('currencies'))) {
            File::makeDirectory(storage_path('currencies'), 0755, true);
        }
        return storage_path("currencies/{$this->getCurrency()}-{$date->format("Ymd")}.json");
    }
}
