<?php

namespace App\Services;

use App\Repositories\CartRepository;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    public function __construct(protected CartRepository $cartRepository) {}

    public function getUserCartItems(int $userId, bool $isWithProduct = false): Collection
    {
        $cartItems = $this->cartRepository->getUserCartItems($userId, $isWithProduct);

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            if (! $product || ! $product->is_available || $product->quantity <= 0) {
                $cartItem->delete();
                throw new \Exception("Product {$product->name} is unavailable");
            }

            if ($product->quantity < $cartItem->quantity) {
                $cartItem->quantity = $product->quantity;
                $cartItem->save();
                throw new \Exception("Not enough stock for product: {$product->name}");
            }
        }

        return $cartItems;
    }
}
