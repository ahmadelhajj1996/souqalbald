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
        foreach($users as $user){
            dump('products');
            Product::factory(2)->for($user, 'seller')->create();
            dump('services');
            Service::factory(2)->for($user, 'user')->create();
            dump('jobs');
            JobAd::factory(2)->for($user, 'user')->create();
        }
    }
}
