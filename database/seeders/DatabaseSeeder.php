<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@tokokomputer.id',
        ]);

        $dataToko = [
            'Laptop' => [
                'ASUS ROG Strix G16',
                'MacBook Pro M3',
            ],
            'Komponen PC' => [
                'NVIDIA RTX 4070 Ti',
                'Processor Intel Core i7-14700K',
            ],
            'Aksesoris' => [
                'Logitech G Pro X Superlight',
            ],
        ];

        foreach ($dataToko as $categoryName => $itemNames) {
            $category = Category::create(['name' => $categoryName]);

            foreach ($itemNames as $itemName) {
                Item::factory()->create([
                    'category_id' => $category->id,
                    'name' => $itemName,
                ]);
            }
        }
    }
}
