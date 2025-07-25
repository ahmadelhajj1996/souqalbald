<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);
        $admin = User::where('email', 'admin@mail.com')->first();
        $admin = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('12345678'),
                'is_active' => 1,
                'email_verified_at' => now(),
                'deleted_at' => null,
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($role);
        }
        User::factory()
            ->create(['email' => 'user@user.com'])
            ->assignRole($role);
    }
}
