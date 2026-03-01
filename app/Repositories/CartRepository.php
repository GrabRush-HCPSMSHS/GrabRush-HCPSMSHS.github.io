<?php

namespace App\Repositories;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CartRepository
{
    public function getUserCartItems(int $userId, bool $isWithProduct = false): Collection
    {
        return CartItem::when($isWithProduct, fn ($query) => $query->with([
            'product:category_id,id,name,quantity,price,is_available',
            'product.image:product_id,path',
        ]))
            ->where('user_id', $userId)
            ->get();
    }
}
