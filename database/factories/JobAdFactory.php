<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JobAdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'added_by' => 1,
            'title' => fake()->title,
            'job_type' => 'full_time',
            'governorate' => fake()->city,
            'location' => fake()->address,
            'long' => fake()->longitude(33.520000, 33.530000),
            'lat' => fake()->latitude(36.210000, 36.220000),
            'salary' => mt_rand(100, 450),
            'education' => fake()->word,
            'experience' => fake()->sentence,
            'skills' => fake()->sentence,
            'description' => fake()->paragraph(2),
            'work_hours' => mt_rand(35, 60),
            'start_date' => fake()->date,
            'phone_number' => fake()->regexify('09[1-9]{1}\d{7}'),
            'email' => fake()->email,
            'job_title' => fake()->jobTitle,
            'type' => 'job_vacancy',
            'is_active' => true,

        ];
    }
}
