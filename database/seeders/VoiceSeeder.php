<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class VoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            DB::table('voices')->insert([
                'customer_id' => 1,
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'voice_file' => 'default/voice/voice1' . '.mp3',
                'duration' => $faker->numberBetween(60, 600),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
