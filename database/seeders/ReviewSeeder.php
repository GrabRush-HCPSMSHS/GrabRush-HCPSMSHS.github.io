<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $customers = User::where('role', UserRole::Customer)->get();
        $reviews = [];

        foreach ($products as $product) {
            foreach ($customers as $customer) {
                $reviews[] = [
                    'product_id' => $product->id,
                    'user_id' => $customer->id,
                    'rating' => rand(1, 5),
                    'comment' => fake()->paragraph,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('reviews')->insert($reviews);
    }
}
