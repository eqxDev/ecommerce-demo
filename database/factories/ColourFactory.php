<?php

namespace Database\Factories;

use App\Models\Colour;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColourFactory extends Factory
{
    protected $model = Colour::class;

    public function definition()
    {
        return [
            'name' => $this->faker->safeColorName,
            'hex' => $this->faker->hexColor,
        ];
    }
}
