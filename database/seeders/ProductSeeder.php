<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wineNames = [
            'Château Margaux',
            'gmaili',
            'Opus One',
            'Sassicaia',
            'Vega Sicilia',
            'Penfolds Grange',
            'Château Lafite Rothschild',
            'Tempranillo',
            'Merlot',
            'Garnacha'
        ];

        $types = ['Appellation', 'AOC', 'IGP'];
        $colors = ['Red', 'White', 'Rosé', 'Sparkling'];
        $soilTypes = ['Clay', 'Limestone', 'Granite', 'Sand', 'Silt'];

        for ($i = 0; $i < 10; $i++) {
            DB::table('products')->insert([
                'user_id' => 4,
                'wine_name' => $wineNames[$i],
                'cuvee' => 'Cuvée ' . Str::random(5),
                'type' => $types[array_rand($types)],
                'color' => $colors[array_rand($colors)],
                'soil_type' => $soilTypes[array_rand($soilTypes)],
                'harvest_ageing' => 'Harvested in ' . rand(2010, 2023) . ', aged for ' . rand(1, 10) . ' years',
                'food' => 'Pairs well with ' . ['steak', 'seafood', 'poultry', 'cheese', 'chocolate'][array_rand([0, 1, 2, 3, 4])],
                'tasting_notes' => 'Notes of ' . ['blackberry', 'cherry', 'vanilla', 'oak', 'spice'][array_rand([0, 1, 2, 3, 4])] . ' with a ' . ['long', 'short', 'medium'][array_rand([0, 1, 2])] . ' finish',
                'awards' =>'IWSC',
                'image' => '/default/product/image.jpg',
                'company_name' => 'Winery ' . Str::random(5),
                'address' => rand(100, 999) . ' Vineyard St, ' . ['Bordeaux', 'Burgundy', 'Tuscany', 'Napa', 'Rioja'][array_rand([0, 1, 2, 3, 4])],
                'phone' => '+018000000'. rand(1000, 9999),
                'email' => 'Wine' . Str::random(1) . @'gmail' . '.com',
                'website' => 'https://www.' . Str::random(5) . '.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
