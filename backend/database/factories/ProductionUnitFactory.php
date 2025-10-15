<?php

namespace Database\Factories;

use App\Models\ProductionUnit;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductionUnit>
 */
class ProductionUnitFactory extends Factory
{
    protected $model = ProductionUnit::class;

    public function definition(): array
    {
        return [
            'crop_name' => $this->faker->randomElement(['Soja', 'Milho', 'Feijão', 'Algodão', 'Arroz']),
            'total_area_ha' => $this->faker->randomFloat(2, 0.5, 500),
            'geographic_coordinates' => $this->faker->optional()->latitude() . ',' . $this->faker->optional()->longitude(),
            'property_id' => Property::factory(),
        ];
    }
}


