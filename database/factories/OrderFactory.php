<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'receipt' => $this->faker->unique()->randomNumber(8),
            'status' => $this->faker->randomElement(OrderStatus::cases()),
        ];
    }
}
