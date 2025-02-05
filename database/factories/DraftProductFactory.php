<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\DraftProduct;
use App\Models\Molecule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DraftProductFactory extends Factory
{
    protected $model = DraftProduct::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'manufacturer' => $this->faker->company,
            'mrp' => $this->faker->randomFloat(2, 1, 1000),
            'is_published' => false,
            'is_active' => $this->faker->boolean,
            'is_banned' => $this->faker->boolean,
            'is_assured' => $this->faker->boolean,
            'is_discountinued' => $this->faker->boolean,
            'is_refrigerated' => $this->faker->boolean,
            'is_published' => $this->faker->boolean,
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved', 'rejected']),
            'category_id' => Category::factory(),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (DraftProduct $draftProduct) {
            $molecules = Molecule::factory()->count(3)->create();
            $draftProduct->molecules()->attach($molecules->pluck('id')->toArray());
        });
    }
    
}