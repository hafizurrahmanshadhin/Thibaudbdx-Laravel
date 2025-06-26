<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Meeting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $i) {
            $datetime = $faker->dateTimeBetween('now', '+15 days');
            Meeting::create([
                'name' => $faker->name,
                'customer_id' => $i <= 5 ? 1 : 6,
                'user_id' => 4,
                'description' => $faker->sentence,
                'date' => $datetime->format('Y-m-d'),
                'time' => $datetime->format('H:i:s'),
                'reminder' => $faker->boolean(),
                'reminder_time' => $faker->randomElement([5, 10, 15, 20]),
            ]);
        }
    }
}
