<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        Product::factory(30)->make()->each(function ($product) use ($categories) {
            $product->category_id = $categories->random()->id;
            $product->save();

            // Attach 1-3 images per product
            ProductImage::factory(rand(1, 3))->create([
                'product_id' => $product->id,
            ]);
        });
    }
}
