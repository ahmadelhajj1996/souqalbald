<?php

namespace App\Traits;

use App\Models\Cost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait CurrencyRatesHandler
{
	protected function allowedCurrencies(): array
	{
		return ['SYP', 'USD', 'EUR', 'TRY'];
	}

	public function costs()
	{
		return $this->morphMany(Cost::class, 'costable');
	}

	protected static function booted(): void
	{
		static::created(function (Model $self) {
			try {
				defer(fn() => $self->handleCurrencyRates());
			} catch (\Throwable $e) {
				Log::error('Currency Boot Error', ['error' => $e]);
			}
		});
	}

	protected function handleCurrencyRates(): void
	{
		$this->checkIfHasRequiredField();
		$rates = $this->fetchRates();
		$data = $this->calculateConvertedCosts($rates);
		$this->storeCosts($data);
	}

	protected function checkIfHasRequiredField(): void
	{
		if (! Schema::hasColumn($this->getTable(), $this->getFieldToCalculateRatesFor())) {
			throw new \Exception(
				class_basename($this::class) . ' missing field: ' . $this->getFieldToCalculateRatesFor()
			);
		}
	}

	protected function getFieldToCalculateRatesFor(): string
	{
		return property_exists($this, 'FieldToCalculateRatesFor') ?
			$this->FieldToCalculateRatesFor :
			'price';
	}

	protected function fetchRates(): array
	{
		return File::exists($this->todayFilePath())
			? $this->parseRatesFromFile($this->todayFilePath())
			: $this->fetchRatesFromApi() ?? $this->parseRatesFromFile($this->yesterdayFilePath());
	}

	protected function parseRatesFromFile(string $filePath): array
	{
		$json = json_decode(File::get($filePath), true);
		return $this->filterRelevantRates($json['rates'] ?? []);
	}

	protected function fetchRatesFromApi(): ?array
	{
		try {
			$response = Http::retry(2, fn(int $attempt) => $attempt * 100)
				->get("https://open.er-api.com/v6/latest/{$this->getCurrency()}");

			if ($response->successful()) {
				$this->writeRatesFile($response->json());

				return $this->filterRelevantRates($response->json('rates'));
			}

			Log::warning('Currency API failed', ['currency' => $this->getCurrency()]);
		} catch (\Throwable $e) {
			Log::error('Currency API Exception', ['error' => $e]);
		}

		return null;
	}

	protected function writeRatesFile(array $data): void
	{
		File::put($this->todayFilePath(), json_encode($data, JSON_PRETTY_PRINT));
	}

	protected function filterRelevantRates(array $rates): array
	{
		return array_intersect_key($rates, array_flip($this->allowedCurrencies()));
	}

	protected function calculateConvertedCosts(array $rates): array
	{
		$result = [];
		$cost = $this->getCost();
		$baseCurrency = $this->getCurrency();
		foreach ($rates as $currency => $rate) {
			$result[] = [
				'cost' => $cost,
				'from_currency' => $baseCurrency,
				'to_currency' => $currency,
				'rate' => $rate,
				'cost_after_change' => $cost * $rate,
				'is_main' => $currency === $baseCurrency,
			];
		}
		if (empty($result)) {
			Log::info('Currency: Missing rates for ' . class_basename($this::class) . ' id ' . $this->id);
		}
		return $result;
	}

	protected function storeCosts(array $data): void
	{
		$this->costs()->createMany($data);
	}

	protected function getCurrency(): string
	{
		$currency = request()->input('currency');
		return in_array($currency, $this->allowedCurrencies())
			? $currency
			: config('currencies.default', 'TRY');
	}

	protected function getCost(): float
	{
		return (float) $this->{$this->getFieldToCalculateRatesFor()};
	}

	protected function todayFilePath(): string
	{
		return $this->buildFilePath(now());
	}

	protected function yesterdayFilePath(): string
	{
		return $this->buildFilePath(now()->yesterday());
	}

	protected function buildFilePath($date): string
	{
		$directory = 'currencies';
		File::ensureDirectoryExists(storage_path($directory));
		$filename = "{$this->getCurrency()}-{$date->format('Ymd')}.json";
		return storage_path("$directory/$filename");
	}
}
