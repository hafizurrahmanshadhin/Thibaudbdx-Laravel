<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter FREE',
                'billing_interval' => 'month',
                'price' => 0.00,
                'currency' => 'EUR',
                'description' => 'Manage up to 20 contacts, schedule tasks and tastings, and store documents—all for free.',
                'features' => [
                    'Contact import (Manual, CSV, Phone contacts) Max 20 entries',
                    'Scheduling meetings and tastings',
                    'Task creation',
                    'Custom tags',
                    'Notes (text & voice)',
                    'Document library',
                    'Product creation',
                    'Customer history',
                    'Mobile & Web Application'
                ],
                'is_recommended' => false,
                'status' => 'active'
            ],
            [
                'name' => 'Essential',
                'billing_interval' => 'month',
                'price' => 29.90,
                'currency' => 'EUR',
                'description' => 'Everything in starter plus advanced features',
                'features' => [
                    'Contact Import Unlimited',
                    'Business-card scanning',
                    'Customer/prospect geolocation',
                    'Import client history (csv)',
                    'PDF datasheet generation',
                    'Priority email support'
                ],
                'is_recommended' => false,
                'status' => 'active'
            ],
            [
                'name' => 'Premium',
                'billing_interval' => 'month',
                'price' => 59.90,
                'currency' => 'EUR',
                'description' => 'Everything in Essential plus premium features',
                'features' => [
                    'Prospect import via Google Maps',
                    'Sales-route planner',
                    'ERP Integration',
                    'Priority email & video support'
                ],
                'is_recommended' => false,
                'status' => 'active'
            ]
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],   // Condition
                [
                    'billing_interval' => $plan['billing_interval'],
                    'price' => $plan['price'],
                    'currency' => $plan['currency'],
                    'description' => $plan['description'],
                    'features' => json_encode($plan['features']),
                    'is_recommended' => $plan['is_recommended'],
                    'status' => $plan['status'],
                ]
            );
        }
    }
}
