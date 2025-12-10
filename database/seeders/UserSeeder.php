<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Mitra
        User::updateOrCreate(
            ['email' => 'mitra@example.com'],
            [
                'name' => 'Mitra Contoh',
                'password' => Hash::make('password'),
                'role' => 'mitra',
                'phone' => '081234567890',
                'address' => 'Alamat mitra contoh',
                'company_name' => 'Toko Mitra Contoh',
                'company_address' => 'Alamat toko mitra contoh',
                'company_phone' => '0211234567',
                'npwp' => '12.345.678.9-012.000',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Customer
        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer Contoh',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '089876543210',
                'address' => 'Alamat customer contoh',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
