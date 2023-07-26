<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'product_code' => $this->faker->lexify('?????????'),
            'colour_id' => Colour::factory(),
            'size_id' => Size::factory(),
            'stock' => $this->faker->numberBetween(0, 100),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
