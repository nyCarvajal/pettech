<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'created_by' => 1,
            'client_id' => null,
            'name' => fake()->firstName(),
            'species' => fake()->randomElement(['Canino', 'Felino']),
            'breed' => fake()->word(),
            'sex' => fake()->randomElement(['male', 'female', 'unknown']),
            'birthdate' => fake()->optional()->date(),
            'color' => fake()->safeColorName(),
            'notes' => fake()->sentence(),
            'active' => true,
        ];
    }
}
