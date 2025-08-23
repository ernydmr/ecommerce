<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(3,true)),
            'description' => fake()->optional()->paragraph(),
            'price' => fake()->randomFloat(2,10,500),
            'stock_quantity' => fake()->numberBetween(5,40),
            'category_id' => Category::factory(),
        ];
    }
}
