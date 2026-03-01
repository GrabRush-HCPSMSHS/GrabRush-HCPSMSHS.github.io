<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Storage::disk('public_uploads')->exists('product-images')) {
            Storage::disk('public_uploads')->deleteDirectory('product-images');
        }

        if (Storage::disk('public_uploads')->exists('payment-images')) {
            Storage::disk('public_uploads')->deleteDirectory('payment-images');
        }

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            CartItemSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
