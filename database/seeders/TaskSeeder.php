<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 13) as $i) {
            Task::create([
                'name' => 'Task ' . $i,
                'user_id' => 4,
                'customer_id' => $i <= 5 ? 1 : ($i <= 10 ? 6 : null),  // 1-5 → 1, 6-10 → 6, 11-13 → null
                'date' => $faker->dateTimeBetween('now', '+7 days')->format('Y-m-d'),
                'description' => $faker->sentence,
                'status' => 'active',
            ]);
        }
    }
}
