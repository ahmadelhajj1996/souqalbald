<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categoryIds = [
            'Animals & Vet' => 1,
            'Electronics' => 2,
            'Vehicles' => 3,
            'RealEstate' => 4,
            'Entertainment' => 5,
            'Fashion & Beauty' => 6,
            'Furniture & Misc' => 7,
        ];

        $subCategories = [
            ['name' => ['en' => 'animal', 'ar' => 'حيوان'], 'category' => 'Animals & Vet'],
            ['name' => ['en' => 'veterinary', 'ar' => 'بيطري'], 'category' => 'Animals & Vet'],
            ['name' => ['en' => 'supply', 'ar' => 'مستلزمات'], 'category' => 'Animals & Vet'],
            ['name' => ['en' => 'mobile', 'ar' => 'هاتف محمول'], 'category' => 'Electronics'],
            ['name' => ['en' => 'laptop', 'ar' => 'حاسوب محمول'], 'category' => 'Electronics'],
            ['name' => ['en' => 'tv', 'ar' => 'تلفاز'], 'category' => 'Electronics'],
            ['name' => ['en' => 'cars', 'ar' => 'سيارات'], 'category' => 'Vehicles'],
            ['name' => ['en' => 'motorcycles', 'ar' => 'دراجات نارية'], 'category' => 'Vehicles'],
            ['name' => ['en' => 'bicycles', 'ar' => 'دراجات هوائية'], 'category' => 'Vehicles'],
            ['name' => ['en' => 'tires & supplies', 'ar' => 'إطارات ومستلزمات'], 'category' => 'Vehicles'],
            ['name' => ['en' => 'propertys', 'ar' => 'عقارات'], 'category' => 'RealEstate'],
            ['name' => ['en' => 'offices', 'ar' => 'مكاتب'], 'category' => 'RealEstate'],
            ['name' => ['en' => 'lands', 'ar' => 'أراضي'], 'category' => 'RealEstate'],
            ['name' => ['en' => 'playStation', 'ar' => 'بلاي ستيشن'], 'category' => 'Entertainment'],
            ['name' => ['en' => 'musical instruments', 'ar' => 'آلات موسيقية'], 'category' => 'Entertainment'],
            ['name' => ['en' => 'books & magazines', 'ar' => 'كتب ومجلات'], 'category' => 'Entertainment'],
            ['name' => ['en' => 'video games', 'ar' => 'ألعاب فيديو'], 'category' => 'Entertainment'],
            ['name' => ['en' => 'fashion', 'ar' => 'موضة'], 'category' => 'Fashion & Beauty'],
            ['name' => ['en' => 'beauty products', 'ar' => 'منتجات تجميل'], 'category' => 'Fashion & Beauty'],
            ['name' => ['en' => 'Sports', 'ar' => 'رياضة'], 'category' => 'Fashion & Beauty'],
            ['name' => ['en' => 'baby supplies', 'ar' => 'مستلزمات أطفال'], 'category' => 'Fashion & Beauty'],
            ['name' => ['en' => 'medical supplies', 'ar' => 'مستلزمات طبية'], 'category' => 'Fashion & Beauty'],
            ['name' => ['en' => 'miscellaneous', 'ar' => 'متفرقات'], 'category' => 'Furniture & Misc'],
            ['name' => ['en' => 'furniture', 'ar' => 'أثاث'], 'category' => 'Furniture & Misc'],
        ];

        foreach ($subCategories as $subCategory) {
            SubCategory::firstOrCreate([
                'name' => $subCategory['name'],
                'category_id' => $categoryIds[$subCategory['category']],
            ]);
        }
    }
}
