<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sub_category_id' => mt_rand(1, 24),
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

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $sub_category = SubCategory::find($product->sub_category_id);
            $data['category_id'] = $sub_category->category_id;
            $name = $sub_category->name;
            if (in_array($name, ['animal', 'veterinary', 'supply'])) {
                dump('aminal');
                $this->animal($product);
            }
            if (in_array($name, ['mobile', 'laptop', 'tv'])) {
                dump('mobile');
                $this->mobile($product);
            }
            if (in_array($name, ['cars', 'motorcycles', 'bicycles', 'tires & supplies',])) {
                dump('cars');
                $this->car($product);
            }
            if (in_array($name, ['propertys', 'offices', 'lands',])) {
                dump('properties');
                $this->properties($product);
            }
            if (in_array($name, ['playStation', 'musical instruments', 'books & magazines', 'video games',])) {
                dump('playStation');
                $this->playStation($product);
            }
            if (in_array($name, ['fashion', 'beauty products', 'Sports', 'baby supplies', 'medical supplies',])) {
                dump('fashion');
                $this->fashion($product);
            }
            if (in_array($name, ['miscellaneous', 'furniture',])) {
                dump('funiture');
                $this->furniture($product);
            }
        });
    }

    private function animal($product)
    {
        $product->animalProductDetails()->create([
            'type' => fake()->word,
            'brand' => fake()->company,
            'age' => mt_rand(1,10),
            'gender' => fake()->randomElement(['male','female']),
            'service_type' => fake()->word,
            'specialization' => fake()->word,
            'service_provider_name' => fake()->word,
            'work_time' => fake()->word,
            'services_price' => fake()->word,
            'vaccinations' => fake()->word,
            'model_or_size' => fake()->word,
            'color' => fake()->colorName,
            'appropriate_to' => fake()->word,
            'accessories' => fake()->word,
        ]);
    }

    private function mobile($product)
    {
        return $product->deviceDetails()->create([
            'type' => fake()->word,
            'brand' => fake()->company,
            'model' => fake()->word,
            'made_in' => fake()->country,
            'year_of_manufacture' => fake()->year,
            'screen_size' => fake()->word,
            'warranty' => fake()->word,
            'camera' => fake()->word,
            'storage' => fake()->word,
            'color' => fake()->colorName,
            'supports_sim' => fake()->word,
            'operation_system' => fake()->word,
            'screen_card' => fake()->word,
            'ram' => fake()->word,
            'processor' => fake()->word,
            'accessories' => fake()->word,
        ]);
    }

    private function car($product)
    {
        return $product->carProductDetails()->create([
            'type' => fake()->word,
            'brand' => fake()->word,
            'model' => fake()->word,
            'year' => fake()->year,
            'kilometers' => mt_rand(1000,5000),
            'fuel_type' => fake()->word,
            'dipstick' => 'normal',
            'engine_capacity' => mt_rand(1000,5000),
            'num_of_doors' => '4',
            'topology_status' => 'clean',
            'size' => 'size',
            'color' => fake()->colorName,
        ]);
    }

    private function properties($product)
    {
        $product->realEstateProductDetails()->create([
            'type' => fake()->word,
            'ownership' => fake()->word,
            'contract_type' => fake()->word,
            'num_of_room' => mt_rand(1,5),
            'num_of_bathroom' => mt_rand(1,5),
            'num_of_balconies' => mt_rand(1,5),
            'area' => mt_rand(50,150),
            'floor' => mt_rand(1,12),
            'furnished' => fake()->boolean,
            'age_of_construction' => fake()->year,
            'readiness' => fake()->boolean,
            'facade' => fake()->word,
            'nature_of_land' => fake()->word,
            'street_width' => fake()->word,
        ]);
    }

    private function playStation($product)
    {
        $product->entertainmentProductDetails()->create([
            'type' => fake()->word,
            'model' => fake()->word,
            'storage' => fake()->word,
            'attached_games' => fake()->boolean,
            'num_of_accessories_supplied' => fake()->word,
            'warranty' => fake()->word,
            'date_of_purchase' => fake()->date,
            'edition' => fake()->word,
            'color' => fake()->colorName,
            'brand' => fake()->company,
            'accessories' => fake()->word,
            'title_of_book' => fake()->title,
            'language' => fake()->languageCode,
            'number_of_copies' => mt_rand(100,300),
            'author' => fake()->name,
            'publishing_house_and_year' => fake()->date,
            'name' => fake()->name,
            'version' => 'first',
            'online_availability' => fake()->boolean,
        ]);
    }

    private function fashion($product)
    {
        return $product->miscellaneousProductDetails()->create([
            'type' => fake()->word,
            'size' => fake()->word,
            'brand' => fake()->word,
            'model' => fake()->word,
            'season' => fake()->word,
            'color' => fake()->colorName,
            'warranty' => fake()->word,
            'material' => fake()->word,
            'special_characteristics' => fake()->word,
            'accessories' => fake()->word,
            'age_group' => fake()->word,
            'year_of_manufacture' => fake()->year,
            'max_endurance' => fake()->word,
            'compatible_vehicles' => fake()->word,
        ]);
    }

    private function furniture($product)
    {
        return $product->electronicsProductDetails()->create([
            'type' => fake()->word,
            'brand' => fake()->word,
            'model' => fake()->word,
            'year_of_manufacture' => fake()->year,
            'size_or_weight' => mt_rand(5,25),
            'color' => fake()->colorName,
            'warranty' => fake()->word,
            'accessories' => fake()->word,
            'main_specification' => fake()->word,
            'dimensions' => fake()->word,
            'state_specification' => fake()->word,
            'made_from' => fake()->word,
        ]);
    }
}
