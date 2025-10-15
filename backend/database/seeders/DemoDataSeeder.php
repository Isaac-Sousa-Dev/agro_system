<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\Herd;
use App\Models\ProductionUnit;
use App\Models\Property;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 5 agricultores; para cada um, cria propriedades, unidades produtivas e rebanhos
        Farmer::factory()
            ->count(5)
            ->has(
                Property::factory()
                    ->count(2)
                    ->has(ProductionUnit::factory()->count(2), 'productionUnits')
                    ->has(Herd::factory()->count(2), 'herds')
            , 'properties')
            ->create();
    }
}


