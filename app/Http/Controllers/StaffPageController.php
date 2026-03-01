<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Response;

class StaffPageController extends Controller
{
    public function __construct(protected OrderRepository $orderRepository)
    {
    }

    public function index(): Response
    {
        $cards = [
            ['label' => "today's revenue", 'value' => $this->getTotalSales()],
            ['label' => 'pending', 'value' => $this->orderRepository->getOrderCountByStatus(OrderStatus::Pending)],
            ['label' => 'preparing', 'value' => $this->orderRepository->getOrderCountByStatus(OrderStatus::Preparing)],
            ['label' => 'ready', 'value' => $this->orderRepository->getOrderCountByStatus(OrderStatus::Ready)],
        ];

        $orders = $this->orderRepository->getOrders(null, null, true);

        return inertia('Staff/Dashboard', compact('cards', 'orders'));
    }

    public function showOrders(Request $request, string $status): Response
    {
        $validStatuses = array_column(OrderStatus::cases(), 'value');

        abort_unless(in_array($status, $validStatuses), 404);

        $orders = $this->orderRepository->getOrders(status: $status, filterSearch: $request->string('filterSearch'));

        return inertia()->render('Staff/Orders', [
            'status' => $status,
            'orders' => inertia()->merge(fn () => $orders->items()),
            'orders_pagination' => $orders->toArray(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $order->update($validated);

        return back();
    }

    private function getTotalSales(): float
    {
        return (float) OrderItem::query()
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_items.quantity * products.price) as total')
            ->value('total') ?? 0;
    }
}
