<?php

namespace Database\Seeders;

use App\Models\JobAd;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();
        $user = $users->random();
        dump('products');
        Product::factory(10)->for($user, 'seller')->create();
        dump('services');
        Service::factory(5)->for($user, 'user')->create();
        dump('jobs');
        JobAd::factory(5)->for($user, 'user')->create();
    }
}
