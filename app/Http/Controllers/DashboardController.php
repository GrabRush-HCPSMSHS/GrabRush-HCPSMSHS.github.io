<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class DashboardController extends Controller
{
    public function showAdminDashboard(): Response
    {
        $salesPerMonth = OrderItem::select(
            DB::raw('strftime("%Y-%m", orders.created_at) as month'),
            DB::raw('SUM(order_items.quantity * products.price) as total_sales')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [
                Carbon::now()->subMonths(12)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->groupBy(DB::raw('strftime("%Y-%m", orders.created_at)'))
            ->orderBy(DB::raw('strftime("%Y-%m", orders.created_at)'))
            ->get();

        $topCategories = Category::select('categories.name as category_name', DB::raw('COUNT(products.id) as total_sales'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [
                Carbon::now()->subMonths(12)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->groupBy('categories.id')
            ->orderByDesc(DB::raw('COUNT(products.id)'))
            ->limit(10)
            ->get();

        return inertia('Admin/AdminDashboard', compact('salesPerMonth', 'topCategories'));
    }
}
