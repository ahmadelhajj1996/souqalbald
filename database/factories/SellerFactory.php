<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class SellerFactory extends Factory
{

    public function definition(): array
    {
        return [
            'user_id'=>1,
	        'store_owner_name'=>fake()->firstName,
	        'store_name'=>fake()->word,
	        'address'=>fake()->address,
	        'description'=>fake()->sentence,
	        'phone'=>fake()->regexify('09[1-9]{1}\d{7}'),
	        'is_featured'=>fake()->boolean,
        ];
    }


}
