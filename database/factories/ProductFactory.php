<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'image_url' => $this->faker->imageUrl(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $colours = Colour::take($this->faker->numberBetween(1, 4))->get();
            foreach ($colours as $colour) {
                $product->variants()->saveMany($this->generateVariantsForColor($colour));
            }
        });
    }

    protected function generateVariantsForColor($colour): array
    {
        $sizes = Size::all();
        $variants = [];

        foreach ($sizes as $size) {
            if ($this->faker->boolean) { // 50/50 chance to add a size
                $variants[] = ProductVariant::factory()->make([
                    'colour_id' => $colour->id,
                    'size_id' => $size->id,
                ]);
            }
        }

        return $variants;
    }

}
