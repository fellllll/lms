<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genres')->insert([
            [
                'name'=>'Fantasy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'=>'Self-improvement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'=>'Romance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'=>'Comedy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'=>'Science Fiction',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
