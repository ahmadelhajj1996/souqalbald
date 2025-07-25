<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'added_by' => 1,
            'title' => fake()->title,
            'type' => fake()->word(),
            'description' => fake()->sentence,
            'price' => mt_rand(20, 100),
            'governorate' => fake()->city,
            'location' => fake()->address,
            'long' => fake()->longitude(33.520000, 33.530000),
            'lat' => fake()->latitude(36.210000, 36.220000),
            'days_hours' => 2,
            'phone_number' => fake()->regexify('09[1-9]{1}\d{7}'),
            'email' => fake()->email,
            'is_active' => true,

        ];
    }
}
