<?php

namespace Database\Seeders;

use App\Models\JobAd;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();
        foreach ($users as $key => $user) {
            if ($key % 2 == 0) {
                $this->createSeller($user);
            }
            dump('products');
            Product::factory(2)->for($user, 'seller')->create();
            dump('services');
            Service::factory(2)->for($user, 'user')->create();
            dump('jobs');
            JobAd::factory(2)->for($user, 'user')->create();
        }
    }

    private function createSeller($user)
    {
        $role = Role::where('name', 'seller')->where('guard_name', 'api')->first();
        if ($role) {
            dump('creating seller');
            $user->assignRole($role);
            Seller::factory()->for($user, 'user')->create();
        }
    }
}
