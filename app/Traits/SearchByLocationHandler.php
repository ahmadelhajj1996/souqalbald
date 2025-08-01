<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait SearchByLocationHandler
{
    public function scopeLocation(Builder $query, $latitude, $longitude, $distanceMeters = 200)
    {
        try {
            $this->checkIfCordsFieldsExists();
            $data = $this->getBoundingBox($latitude, $longitude, $distanceMeters);

            return $this->search($query, $latitude, $longitude, $data);
        } catch (\Exception $e) {
            error_log(json_encode($e));
            throw $e;
        }
    }

    private function checkIfCordsFieldsExists(): void
    {
        if (! Schema::hasColumns($this->getTable(), ['long', 'lat'])) {
            throw new \Exception(class_basename($this::class).' missing longitude, latitude fields');
        }
    }

    private function getBoundingBox(float $latitude, float $longitude, int $distanceMeters = 200): array
    {
        $distanceKm = $distanceMeters / 1000;
        $earthRadius = 6371; // km
        $lat = deg2rad($latitude);
        $lon = deg2rad($longitude);
        $deltaLat = rad2deg($distanceKm / $earthRadius);
        $deltaLon = rad2deg($distanceKm / ($earthRadius * cos($lat)));

        return [
            'min_lat' => $latitude - $deltaLat,
            'max_lat' => $latitude + $deltaLat,
            'min_lon' => $longitude - $deltaLon,
            'max_lon' => $longitude + $deltaLon,
            'distanceMeters' => $distanceMeters,
            'distanceKm' => $distanceKm,
        ];
    }

    private function search(Builder $query, float $latitude, float $longitude, array $data)
    {
        return $query
            // specify the bounding box
            ->whereBetween('long', [$data['min_lon'], $data['max_lon']])
            ->whereBetween('lat', [$data['min_lat'], $data['max_lat']])
            // return distance in meter
            ->selectRaw(
                '*, CEIL(St_distance_sphere(point(`long`, `lat`), point(?, ?))) as distance',
                [$longitude, $latitude]
            )
            ->having('distance', '<=', $data['distanceMeters']);
    }

    public function getUserDistanceAttribute($lat, $long)
    {
        $rad = M_PI / 180;

        return ceil(acos(
            sin($this->latitude * $rad) * sin($lat * $rad)
                + cos($this->latitude * $rad) * cos($lat * $rad)
                * cos($this->longitude * $rad - $long * $rad)
        ) * 6371); // Kilometers
    }
}
