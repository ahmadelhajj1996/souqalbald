<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['en' => 'Animals & Vet', 'ar' => 'الحيوانات والطب البيطري'],
            ['en' => 'Electronics', 'ar' => 'الإلكترونيات'],
            ['en' => 'Vehicles', 'ar' => 'المركبات'],
            ['en' => 'RealEstate', 'ar' => 'العقارات'],
            ['en' => 'Entertainment', 'ar' => 'الترفيه'],
            ['en' => 'Fashion & Beauty', 'ar' => 'الموضة والجمال'],
            ['en' => 'Furniture & Misc', 'ar' => 'الأثاث والمتفرقات'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
