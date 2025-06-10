<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
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

        // // 3) Assign the “admin” role to that user:
        $user->assignRole('admin');

         // 2) Create an “Admin” user:
        Product::create([
            'uuid'        => '3f1b0c9a-e8f7-4aee-a2d4-b67f5e3c9d1a',
            'name'         => 'Aplu 1.0.0.0',
            'slug'        => 'example-product',
            'icon'        => 'icon.png',
            'version'     => '1.0.0',
            'price'       => 100.00,
            'type'        => 'core',
            'description' => 'This is an example product description.',
            'status'      => 1,
        ]);
    }
}
