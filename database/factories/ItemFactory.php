<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement(['ASUS ROG Strix', 'RTX 4060 Ti', 'Logitech G Pro X', 'RAM Corsair 16GB', 'SSD Samsung 1TB']),
            'price' => fake()->randomFloat(2, 150000, 25000000),
            'stock' => fake()->numberBetween(5, 50),
        ];
    }
}
