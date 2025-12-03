<?php

namespace Database\Seeders;

use DB;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'role_id' => 1, // Admin role
                'email' => 'john.doe@example.com',
                'password' => Hash::make('12345678'),
                'address' => '456 Elm St, Springfield, IL 62704',
                'biography' => 'Experienced administrator with a background in team leadership and operations management.',
            ],
            [
                'name' => 'Jane Smith',
                'role_id' => 2, // User role
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('12345678'),
                'address' => '789 Maple Ave, Centerville, OH 45459',
                'biography' => 'Passionate software developer skilled in web and mobile application development, with an interest in AI and machine learning.',
            ],
        ]);
    }
}
