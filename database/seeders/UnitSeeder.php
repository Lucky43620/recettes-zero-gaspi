<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['code' => 'g', 'label' => 'grammes'],
            ['code' => 'kg', 'label' => 'kilogrammes'],
            ['code' => 'ml', 'label' => 'millilitres'],
            ['code' => 'l', 'label' => 'litres'],
            ['code' => 'cl', 'label' => 'centilitres'],
            ['code' => 'dl', 'label' => 'décilitres'],
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

        \DB::table('units')->insert($units);
    }
}
