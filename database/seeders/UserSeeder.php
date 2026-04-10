<?php

namespace Database\Seeders;

use App\Models\CostCentre;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = CostCentre::first();
        $manager = CostCentre::skip(1)->first();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@fms.com',
            'password' => bcrypt('password'),
            'cost_centre_id' => $admin?->id,
            'role' => 'super_admin',
        ]);

        User::create([
            'name' => 'Finance User',
            'email' => 'finance@fms.com',
            'password' => bcrypt('password'),
            'cost_centre_id' => $admin?->id,
            'role' => 'finance',
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@fms.com',
            'password' => bcrypt('password'),
            'cost_centre_id' => $manager?->id,
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@fms.com',
            'password' => bcrypt('password'),
            'cost_centre_id' => $manager?->id,
            'role' => 'user',
        ]);
    }
}
