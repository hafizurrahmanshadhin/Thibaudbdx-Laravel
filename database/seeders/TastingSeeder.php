<?php

namespace Database\Seeders;

use App\Models\Tasting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TastingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 13) as $i) {
            Tasting::create([
                'customer_id' => $i <= 5 ? 1 : ($i <= 10 ? 6 : 1),
                'user_id' => 4,
                'name' => 'Tasting ' . $i,
                'date' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
                'time' => $faker->time('H:i'),
                'product_id' => json_encode([
                    $faker->numberBetween(1, 5),
                    $faker->numberBetween(6, 8),
                ]),
                'description' => $faker->sentence,
                'status' => 'active',
            ]);
        }
    }
}
