<?php

namespace Database\Factories;

use App\Models\OrderItems;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItems>
 */
class OrderItemsFactory extends Factory
{
    protected $model = OrderItems::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productPrice = $this->faker->randomFloat(2, 300, 200000);
        $product = Product::factory(['price' => $productPrice]);
        return [
            'order_id' => Order::factory(),
            'product_id' => $product,
            'quantity' => $this->faker->randomDigitNotNull(),
            'actual_price' => $productPrice,
        ];
    }
}
