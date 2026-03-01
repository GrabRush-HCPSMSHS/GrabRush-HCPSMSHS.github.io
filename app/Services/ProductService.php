<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(protected ProductRepository $productRepository) {}

    public function findOneById(string $id, array $with = []): ?Product
    {
        $query = Product::query();

        if (! empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    public function updateStock(Product $product, int $quantity): void
    {
        $product->decrement('quantity', $quantity);

        if ($product->fresh()->quantity <= 0) {
            $product->update(['is_available' => false]);
        }
    }
}
