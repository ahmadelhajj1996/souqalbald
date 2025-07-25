<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sub_category_id' => 1,
            'title' => fake()->word,
            'governorate' => 'دمشق',
            'address_details' => fake()->address,
            'long' => fake()->longitude(33.520000, 33.530000),
            'lat' => fake()->latitude(36.210000, 36.220000),
            'description' => fake()->sentence,
            'phone_number' => fake()->regexify('09[1-9]{1}\d{7}'),
            'email' => fake()->email,
            'category_id' => 1,
            'price' => mt_rand(20, 100),
            'price_type' => 'free',
            'state' => 'new',
            'added_by' => 1,
            'views' => 1,
            'is_active' => true,

        ];
    }
}
