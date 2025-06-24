<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $customerData = [];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia'];
        $address = ['Dhaka', 'Chittagong', 'Comilla', 'Mymensingh', 'Rangpur', 'Sylhet'];

        // Ensure user with ID 4 exists
        if (!\App\Models\User::find(4)) {
            \App\Models\User::factory()->create(['id' => 4]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $contactType = ($i <= 5) ? 'prospect' : 'customer';
            $cityIndex = $i % count($cities);
            $addressIndex = $i % count($address);
            $customerData[] = [
                'user_id' => 4,
                'contact_type' => $contactType,
                'company_name' => "Company {$i} LLC",
                'owner_name' => fake()->name(),
                'address' => $address[$addressIndex],
                'city' => $cities[$cityIndex],
                'zip_code' => str_pad($i, 5, '0', STR_PAD_LEFT),
                'phone' => '018000000000',
                'email' => "customer{$i}@gmail.com",
                'website' => "https://company{$i}.com",
                'tag_id' => json_encode([rand(1, 5), rand(6, 10)]),
                'description' => fake()->sentence(10),
                'image' => '/default/customer/defult_image.png',
                'longitude' => -73.985130 + ($i * 0.01),
                'latitude' => 40.748817 + ($i * 0.01),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($customerData, 50) as $data) {
            Customer::insert($data);
        }
    }
}
