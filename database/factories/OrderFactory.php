<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::all()->random()->id,
            'total' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'data' => \Crypt::encrypt( [
                'billing_name' => $this->faker->name,
                'billing_address' => $this->faker->streetAddress,
                'billing_address2' => $this->faker->streetAddress,
                'billing_city' => $this->faker->city,
                'billing_county' => $this->faker->city,
                'billing_postcode' => $this->faker->postcode,

                'shipping_copy' => $this->faker->boolean(),
                'shipping_name' => $this->faker->name,
                'shipping_address' => $this->faker->streetAddress,
                'shipping_address2' => $this->faker->streetAddress,
                'shipping_city' => $this->faker->city,
                'shipping_county' => $this->faker->city,
                'shipping_postcode' => $this->faker->postcode,
            ])
        ];
    }
}
