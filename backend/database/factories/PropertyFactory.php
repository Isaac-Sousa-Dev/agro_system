<?php

namespace Database\Factories;

use App\Models\Farmer;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Farm',
            'municipality' => $this->faker->randomElement(['Fortaleza', 'Caucaia', 'Juazeiro do Norte', 'Iguatu', 'Sobral', 'Quixadá', 'Pacajus']),
            'state' => $this->faker->randomElement(['CE', 'PI', 'RN', 'PE', 'PB', 'SE', 'AL', 'BA', 'MA']),
            'state_registration' => $this->faker->optional()->bothify('SR-#####'),
            'total_area' => $this->faker->randomFloat(2, 1, 1000),
            'farmer_id' => Farmer::factory(),
        ];
    }
}



