<?php

namespace Database\Factories;

use App\Models\Molecule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoleculeFactory extends Factory
{
    protected $model = Molecule::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(90),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
