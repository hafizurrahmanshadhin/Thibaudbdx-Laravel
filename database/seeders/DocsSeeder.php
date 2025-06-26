<?php

namespace Database\Seeders;

use App\Models\Docs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $i) {
            $customerId = $i <= 5 ? 1 : 6;
            Docs::create([
                'customer_id' => $customerId,
                'user_id' => 4,
                'file' => "/default/default/docoments/docs{$i}.pdf",
                'status' => 'active',
            ]);
        }
    }
}
