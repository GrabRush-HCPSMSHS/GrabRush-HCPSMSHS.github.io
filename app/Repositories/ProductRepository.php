<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function findAll(?string $name = null, ?Category $category = null, ?bool $isStatusAvailable = null, ?bool $hasQuantity = true): LengthAwarePaginator
    {
        $products = Product::select('category_id', 'id', 'name', 'description', 'price', 'quantity', 'is_available')
            ->with([
                'category:id,name',
                'image:product_id,path',
            ])
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($category, function ($query) use ($category) {
                $query->whereBelongsTo($category);
            })
            ->when(! is_null($isStatusAvailable), function ($query) use ($isStatusAvailable) {
                $query->where('is_available', $isStatusAvailable);
            })
            ->when($hasQuantity, function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $product->category_name = $product->category ? $product->category->name : null;

            return $product;
        });

        return $products;
    }
}
