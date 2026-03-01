<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\PaymentImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'path' => $this->faker->imageUrl(),
        ];
    }
}
