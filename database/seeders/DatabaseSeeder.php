<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Colour;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

      collect(['S','M','L','XL'])->each(fn ($size) => Size::create(['name' => $size]));

        $colors = [
            'Red' => '#FF0000',
            'Green' => '#008000',
            'Blue' => '#0000FF',
            'Yellow' => '#FFFF00',
            'White' => '#FFFFFF',
            'Black' => '#000000',
        ];
        foreach ($colors as $name => $hex) {
            Colour::create([
                'name' => $name,
                'hex' => $hex,
            ]);
        }

        Product::factory()->count(25)->create();

        User::factory()->count(5)->create();
        Order::factory()->count(20)->create()->each(function ($order) {
            $order->items()->saveMany(OrderItem::factory()->count(2)->make());
        });
    }
}
