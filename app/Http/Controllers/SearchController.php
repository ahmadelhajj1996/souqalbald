<?php

namespace App\Http\Controllers;

use App\Models\JobAd;
use App\Models\Product;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use ApiResponseTrait;

    private ?string $title = null;

    private ?string $type = null;

    private ?float $lat = null;

    private ?float $long = null;

    private ?int $distance = null;

    public function byTitle(Request $request, ?string $type = null)
    {
        $request->validate([
            'title' => ['required', 'string'],
        ]);
        $this->type = $request->type;
        $this->title = $request->title;
        try {
            $result = match ($type) {
                'product' => $this->productsByTitle(),
                'job' => $this->jobsByTitle(),
                'service' => $this->servicesByTitle(),
                default => $this->productsByTitle(),
            };

            return $this->successResponse(
                result: $this->toResource($result),
            );
        } catch (\Exception $e) {
            return $this->errorResponse("Title Search Faild: {$e->getMessage()} ");
        }
    }

    public function byLocation(Request $request, ?string $type = null)
    {
        $request->validate([
            'distance' => ['required', 'numeric', 'min:300'],
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
        ]);
        $this->type = $request->type;
        $this->lat = $request->lat;
        $this->long = $request->long;
        $this->distance = $request->distance;
        try {
            $result = match ($type) {
                'product' => $this->productsByLocation(),
                'job' => $this->jobsByLocation(),
                'service' => $this->servicesByLocation(),
                default => $this->allByLocation(),
            };

            return $this->successResponse(
                result: $this->toResource($result),
            );
        } catch (\Exception $e) {
            return $this->errorResponse("Location Search Faild: {$e->getMessage()}");
        }
    }

    public function allByLocation(): array
    {
        return [
            ...$this->productsByLocation(),
            ...$this->jobsByLocation(),
            ...$this->servicesByLocation(),
        ];
    }

    private function productsByLocation(): Collection
    {
        return Product::location($this->lat, $this->long, $this->distance)->get();
    }

    private function jobsByLocation(): Collection
    {
        return JobAd::location($this->lat, $this->long, $this->distance)->get();
    }

    private function servicesByLocation(): Collection
    {
        return Service::location($this->lat, $this->long, $this->distance)->get();
    }

    private function productsByTitle(): array
    {
        return Product::whereLike('title', $this->title)->get();
    }

    private function jobsByTitle(): array
    {
        return JobAd::whereLike('title', $this->title)->get();
    }

    private function servicesByTitle(): array
    {
        return Service::whereLike('title', $this->title)->get();
    }

    private function toResource($result): array
    {
        $data = [];
        foreach ($result as $item) {
            $class = lcfirst(class_basename($item));
            $data[] = match ($class) {
                'jobAd' => $this->jobResource($item),
                'product' => $this->productResource($item),
                'service' => $this->serviceResource($item),
                default => throw new \Exception('Resource type invalid'),
            };
        }

        return $data;
    }

    private function productResource(Product $product): array
    {
        return [
            'type' => 'product',
            'id' => $product->id,
            'title' => $product->title ?? 'title',
            'distance' => $product->distance ?? 'distance',
            'long' => $product->long ?? 00.0000,
            'lat' => $product->lat ?? 00.0000,
            'price' => $product->price ?? 'price',
            'category' => $product->category->name ?? 'category',
        ];
    }

    private function serviceResource(Service $service): array
    {
        return [
            'type' => 'service',
            'id' => $service->id,
            'title' => $service->title ?? 'title',
            'distance' => $service->distance ?? 'distance',
            'long' => $service->long ?? 00.0000,
            'lat' => $service->lat ?? 00.0000,
            'price' => $service->price ?? 'price',
            'service_type' => $service->type ?? 'type',
        ];
    }

    private function jobResource(JobAd $job): array
    {
        return [
            'type' => 'job',
            'id' => $job->id,
            'title' => $job->title ?? 'title',
            'distance' => $job->distance ?? 'distance',
            'long' => $job->long ?? 00.0000,
            'lat' => $job->lat ?? 00.0000,
            'salary' => $job->salary ?? 'salary',
            'job_title' => $job->job_title ?? 'job_title',
        ];
    }
}
