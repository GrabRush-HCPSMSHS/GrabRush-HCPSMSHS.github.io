<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();
        $orderItems = [];

        foreach ($orders as $order) {
            $randomProducts = $products->random(3);

            foreach ($randomProducts as $product) {
                $orderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('order_items')->insert($orderItems);
    }
}
