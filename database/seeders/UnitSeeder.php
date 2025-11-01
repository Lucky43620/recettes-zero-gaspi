<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'g', 'label' => 'gramme(s)'],
            ['code' => 'kg', 'label' => 'kilogramme(s)'],
            ['code' => 'mg', 'label' => 'milligramme(s)'],

            ['code' => 'ml', 'label' => 'millilitre(s)'],
            ['code' => 'l', 'label' => 'litre(s)'],
            ['code' => 'cl', 'label' => 'centilitre(s)'],
            ['code' => 'dl', 'label' => 'décilitre(s)'],

            ['code' => 'tsp', 'label' => 'cuillère à café'],
            ['code' => 'tbsp', 'label' => 'cuillère à soupe'],
            ['code' => 'cup', 'label' => 'tasse'],

            ['code' => 'piece', 'label' => 'pièce(s)'],
            ['code' => 'slice', 'label' => 'tranche(s)'],
            ['code' => 'pinch', 'label' => 'pincée'],
            ['code' => 'bunch', 'label' => 'botte'],
            ['code' => 'can', 'label' => 'boîte'],
            ['code' => 'jar', 'label' => 'pot'],
            ['code' => 'package', 'label' => 'paquet'],
        ];

        foreach ($units as $unit) {
            DB::table('units')->updateOrInsert(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
