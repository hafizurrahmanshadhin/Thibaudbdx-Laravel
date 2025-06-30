<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $sales = [
            [
                'customer_id' => 1,
                'user_id' => 4,
                'date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'price' => 1200.00,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'customer_id' => 1,
                'user_id' => 4,
                'date' => Carbon::now()->subDays(8)->format('Y-m-d'),
                'price' => 850.50,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'customer_id' => 1,
                'user_id' => 4,
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'price' => 1500.00,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'customer_id' => 1,
                'user_id' => 4,
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'price' => 750.25,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'customer_id' => 1,
                'user_id' => 4,
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'price' => 2000.00,
                'status' => 'cancelled',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('sales')->insert($sales);
    }
}
