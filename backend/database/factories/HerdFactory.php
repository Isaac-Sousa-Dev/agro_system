<?php

namespace Database\Factories;

use App\Models\Herd;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Herd>
 */
class HerdFactory extends Factory
{
    protected $model = Herd::class;

    public function definition(): array
    {
        return [
            'species' => $this->faker->randomElement(['Bovino', 'Caprino', 'Ovino', 'Suíno', 'Aves']),
            'quantity' => $this->faker->numberBetween(5, 500),
            'purpose' => $this->faker->randomElement(['Leite', 'Corte', 'Reprodução', 'Ovos']),
            'update_date' => $this->faker->dateTimeBetween('-1 year')->format('Y-m-d'),
            'property_id' => Property::factory(),
        ];
    }
}



