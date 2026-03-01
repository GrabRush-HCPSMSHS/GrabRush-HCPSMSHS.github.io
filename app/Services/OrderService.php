<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentImage;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected ProductService $productService,
        protected OrderRepository $orderRepository,
        protected PaymentImageService $paymentImageService
    ) {}

    public function createOrder(int $userId, ?PaymentImage $paymentImage, string $paymentMethod, ?string $notes): Order
    {
        return DB::transaction(function () use ($userId, $paymentImage, $paymentMethod, $notes) {
            $order = Order::create([
                'receipt' => strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)).'-'.now()->format('Ymdhis').'-'.auth()->user()->email,
                'user_id' => $userId,
                'payment_method' => $paymentMethod,
                'notes' => $notes,
            ]);

            if ($paymentImage) {
                $this->paymentImageService->assignImageToOrder($paymentImage->id, $order->id);
            }

            $this->attachCartItemsToOrder($order);

            return $order;
        });
    }

    private function attachCartItemsToOrder(Order $order): void
    {
        $cartItems = $this->cartService->getUserCartItems($order->user_id);

        foreach ($cartItems as $item) {
            $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();

            if ($product->quantity < $item->quantity) {
                throw new \Exception("Not enough stock for product {$product->id}");
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);

            $this->productService->updateStock($item->product, $item->quantity);
        }

        CartItem::whereBelongsTo(auth()->user())->delete();
    }
}
