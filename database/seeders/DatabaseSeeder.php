<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Ensure roles exist first:
        $this->call([
            RoleSeeder::class,
        ]);

        // 2) Create an “Admin” user:
        $user = User::factory()->create([
            'name'         => 'Admin',
            'email'        => 'admin@example.com',
            'country_code' => '+91',
            'phone'        => '0000000000',
            'password'     => bcrypt('admin@123'),
        ]);

        // 3) Assign the “admin” role to that user:
        $user->assignRole('admin');

    }
}
