<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

/**
 * undocumented class
 *
 * @author
 **/
class CustomerPageController
{
    public function __construct(protected ProductRepository $productRepository, protected CategoryRepository $categoryRepository) {}

    public function showHome(): Response
    {
        $user = auth()->user();
        $orders = $user->orders();

        $completedOrdersWithItems = $orders
            ->where('status', OrderStatus::Complete->value)
            ->with('orderItems.product')
            ->get();

        $totalMoneySpent = $user->orders()
            ->where('status', OrderStatus::Complete->value)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('order_items.quantity * products.price'));

        $pendingOrders = $orders->where('status', OrderStatus::Pending->value)->count();
        $preparingOrders = $orders->where('status', OrderStatus::Preparing->value)->count();
        $readyOrders = $orders->where('status', OrderStatus::Ready->value)->count();

        $categories = $this->categoryRepository->findAllWithProducts();

        return inertia()->render('Customer/Home', [
            'categories' => inertia()->merge(fn () => $categories->items()),
            'categories_pagination' => $categories->toArray(),
            'completedOrdersWithItems' => $completedOrdersWithItems,
            'pendingOrders' => $pendingOrders,
            'preparingOrders' => $preparingOrders,
            'readyOrders' => $readyOrders,
            'totalMoneySpent' => $totalMoneySpent,
        ]);
    }

    public function showCategories(Request $request): Response
    {
        $categories = $this->categoryRepository->findAll($request->string('filterCategoryName'), true);

        return inertia('Customer/Categories', compact('categories'));
    }

    public function showProductOfCategory(Request $request, Category $category): Response
    {
        $products = $this->productRepository->findAll(
            $request->string('filterProductName'),
            $category,
            true,
        );

        return inertia()->render('Customer/CategoryProducts', [
            'category' => $category,
            'products' => inertia()->merge(fn () => $products->items()),
            'products_pagination' => $products->toArray(),
        ]);
    }

    public function showProduct(Product $product): Response
    {
        $product->load(['image', 'category', 'reviews.user']);

        Gate::authorize('view', $product);

        $cartItem = CartItem::whereBelongsTo($product)->whereBelongsTo(auth()->user())->first();

        return inertia('Customer/ProductShow', [
            'product' => $product,
            'cartItem' => $cartItem,
            'reviews' => $product->reviews,
        ]);
    }
}
