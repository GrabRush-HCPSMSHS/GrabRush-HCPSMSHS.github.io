<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\PaymentImage;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CustomerOrderController extends Controller
{
    public function __construct(protected OrderService $orderService, protected OrderRepository $orderRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $orders = $this->orderRepository->getOrders(
            user: auth()->user(),
            filterSearch: $request->string('filterSearch')
        );

        return inertia()->render('Customer/Orders', [
            'orders' => inertia()->merge(fn () => $orders->items()),
            'orders_pagination' => $orders->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        // implement background processing
        try {
            $paymentImageId = $request->input('payment_image_id');
            $paymentImage = $paymentImageId ? PaymentImage::findOrFail($paymentImageId) : null;

            $this->orderService->createOrder(
                auth()->id(),
                $paymentImage,
                $request->string('payment_method'),
                $request->string('notes')
            );
        } catch (\Exception $e) {
            return redirect()->route('customer.cart-items.index')
                ->withErrors(['order_error' => $e->getMessage()]);
        }

        return redirect()->route('customer.orders.index');
    }
}
