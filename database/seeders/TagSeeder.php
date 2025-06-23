<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $tagNames = [
            'restaurant',
            'Technology',
            'Health',
            'Education',
            'Science',
            'Travel',
            'Food',
            'Sports',
            'Music',
            'Entertainment',
        ];

        foreach ($tagNames as $name) {
            DB::table('tags')->insert([
                'user_id' =>4,
                'name' => $name,
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
