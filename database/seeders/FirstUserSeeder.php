<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'First User Name',
            'mobile_number' => '1234567890', // Replace with your desired mobile number format
            'password' => Hash::make('password'), // Replace with your desired password
            'sponsor_id' => 'your_sponsor_id', // Replace with your desired sponsor_id
            'dob' => '1990-01-01', // Replace with your desired date of birth format
            'address' => 'Your Address', // Replace with your desired address
            'referer_id' => null, // or leave it empty if allowed by your schema
            'balance' => 0.00, // Set initial balance as needed
        ]);
    }
}
