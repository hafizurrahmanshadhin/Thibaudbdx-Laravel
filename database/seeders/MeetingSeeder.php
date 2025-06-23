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


        $customerId = Customer::inRandomOrder()->first()->id;

        foreach (range(1, 5) as $i) {
            Meeting::create([
                'name' => $faker->name,
                'customer_id' => 1,
                'description' => $faker->sentence,
                'date' => $faker->date(),
                'time' => $faker->time(),
                'reminder' => $faker->boolean(),
                'reminder_time' => $faker->randomElement([5, 10, 15, 20]),
                'status' => 'active',
            ]);
        }
    }
}
