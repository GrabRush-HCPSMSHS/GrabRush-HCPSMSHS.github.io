<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function findAll(?string $name = null, ?bool $isWithProductCount = null, ?bool $isPaginated = null, ?int $pages = 10): Collection|LengthAwarePaginator
    {
        $query = Category::query();

        if ($isWithProductCount) {
            $query->withCount('products');
        }

        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        return $isPaginated ? $query->paginate($pages) : $query->get();
    }

    public function findAllWithProducts(): LengthAwarePaginator
    {
        $categories = Category::select('id', 'name')
            ->with([
                'products' => function ($query) {
                    $query->select('id', 'category_id', 'name', 'description', 'price', 'quantity', 'is_available')
                        ->where('quantity', '>', 0)
                        ->where('is_available', true);
                },
                'products.image' => function ($query) {
                    $query->select('product_id', 'path');
                },
            ])
            ->whereHas('products', function ($query) {
                $query->where('quantity', '>', 0)
                    ->where('is_available', true);
            })
            ->paginate(10);

        $categories->getCollection()->each(function ($category) {
            $category->products->each(function ($product) use ($category) {
                $product->category_name = $category->name;
            });
        });

        return $categories;
    }
}
