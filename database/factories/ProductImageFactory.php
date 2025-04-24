<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   protected $model = \App\Models\ProductImage::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            // 'image_path' => $this->faker->imageUrl(640, 480, 'products', true), // Placeholder
            'image_path' => 'https://placehold.co/640x480/png?text=Test+Image',
        ];
    }
}
