<?php

namespace Database\Seeders;

use App\Enums\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'g', 'name' => 'grammes', 'abbreviation' => 'g', 'type' => UnitType::WEIGHT->value],
            ['code' => 'kg', 'name' => 'kilogrammes', 'abbreviation' => 'kg', 'type' => UnitType::WEIGHT->value],
            ['code' => 'mg', 'name' => 'milligrammes', 'abbreviation' => 'mg', 'type' => UnitType::WEIGHT->value],

            ['code' => 'ml', 'name' => 'millilitres', 'abbreviation' => 'ml', 'type' => UnitType::VOLUME->value],
            ['code' => 'l', 'name' => 'litres', 'abbreviation' => 'L', 'type' => UnitType::VOLUME->value],
            ['code' => 'cl', 'name' => 'centilitres', 'abbreviation' => 'cl', 'type' => UnitType::VOLUME->value],
            ['code' => 'dl', 'name' => 'décilitres', 'abbreviation' => 'dl', 'type' => UnitType::VOLUME->value],

            ['code' => 'tsp', 'name' => 'cuillère à café', 'abbreviation' => 'c.à.c', 'type' => UnitType::VOLUME->value],
            ['code' => 'tbsp', 'name' => 'cuillère à soupe', 'abbreviation' => 'c.à.s', 'type' => UnitType::VOLUME->value],
            ['code' => 'cup', 'name' => 'tasse', 'abbreviation' => 'tasse', 'type' => UnitType::VOLUME->value],

            ['code' => 'piece', 'name' => 'pièce(s)', 'abbreviation' => 'pc', 'type' => UnitType::PIECE->value],
            ['code' => 'slice', 'name' => 'tranche(s)', 'abbreviation' => 'tranche', 'type' => UnitType::PIECE->value],
            ['code' => 'pinch', 'name' => 'pincée', 'abbreviation' => 'pincée', 'type' => UnitType::OTHER->value],
            ['code' => 'bunch', 'name' => 'botte', 'abbreviation' => 'botte', 'type' => UnitType::OTHER->value],
            ['code' => 'can', 'name' => 'boîte', 'abbreviation' => 'boîte', 'type' => UnitType::OTHER->value],
            ['code' => 'jar', 'name' => 'pot', 'abbreviation' => 'pot', 'type' => UnitType::OTHER->value],
            ['code' => 'package', 'name' => 'paquet', 'abbreviation' => 'paquet', 'type' => UnitType::OTHER->value],
        ];

        foreach ($units as $unit) {
            DB::table('units')->updateOrInsert(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
