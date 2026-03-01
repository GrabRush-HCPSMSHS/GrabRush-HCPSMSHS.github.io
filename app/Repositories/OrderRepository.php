<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function getOrders(?User $user = null, ?string $status = null, ?string $filterSearch = null): LengthAwarePaginator
    {
        return Order::with([
            'user:id,name,email',
            'orderItems.product:category_id,id,name,price',
            'orderItems.product.image:product_id,path',
            'orderItems.product.category',
            'paymentImage:order_id,id,path',
        ])
            ->when($user, function ($query) use ($user) {
                $query->whereBelongsTo($user);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($filterSearch, function ($query) use ($filterSearch) {
                $query->where(function ($query) use ($filterSearch) {
                    $query->where('receipt', 'like', "%$filterSearch%")
                        ->orWhereHas('user', function ($query) use ($filterSearch) {
                            $query->where('name', 'like', "%$filterSearch%");
                        });
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function getOrderCountByStatus(OrderStatus $status): int
    {
        return Order::where('status', $status->value)->count();
    }
}
