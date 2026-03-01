<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', UserRole::Customer)->get();
        $products = Product::all();
        $cartItems = [];

        foreach ($customers as $customer) {
            $randomProducts = $products->random(5);
            foreach ($randomProducts as $product) {
                $cartItems[] = [
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('cart_items')->insert($cartItems);
    }
}
