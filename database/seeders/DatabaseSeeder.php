<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            RolesTableSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            UserSeeder::class,
        ]);
        if (! Client::where('name', 'Laravel')->exists()) {
            dump('passport client');
            Artisan::call('passport:client --personal -n');
        }
    }
}
