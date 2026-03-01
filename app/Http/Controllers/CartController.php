<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response|RedirectResponse
    {
        try {
            $cartItems = $this->cartService->getUserCartItems(auth()->id(), true);
        } catch (\Exception $e) {
            return redirect()->route('customer.cart-items.index')
                ->withErrors(['order_error' => $e->getMessage()]);
        }

        return inertia('Customer/Cart', compact('cartItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), [
            'user_id' => auth()->id(),
        ]);

        CartItem::create($data);

        return redirect()->route('customer.products.show', $request->integer('product_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $cartItem->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem): RedirectResponse
    {
        Gate::authorize('delete', $cartItem);
        $cartItem->delete();

        return back();
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        CartItem::whereBelongsTo(auth()->user())->whereIn('id', $ids)->delete();

        return redirect()->route('customer.cart-items.index');
    }
}
