<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@globalspay.com', // Replace with admin email
            'password' => Hash::make('globalspay@01#'), // Replace with desired admin password
            // Add other fields as needed
        ]);
    }
}
