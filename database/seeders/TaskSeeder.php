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


        foreach (range(1, 5) as $i) {
            Task::create([
                'name' => 'Task ' . $i,
                'customer_id' => 1,
                'date' => $faker->date(),
                'description' => $faker->sentence,
                'status' => 'active',
            ]);
        }
    }
}
