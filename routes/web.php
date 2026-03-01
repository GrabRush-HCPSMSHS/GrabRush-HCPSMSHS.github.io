<?php

use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerPageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentImageController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffPageController;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'products' => Cache::remember('welcome_products', now()->addDay(), function () {
            return Product::select('products.*', 'categories.name as category_name')
                ->with('image')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->latest()
                ->take(8)
                ->get();
        }),
        'reviews' => Cache::remember('welcome_reviews', now()->addDay(), function () {
            return Review::with('user')->latest()->take(5)->get();
        }),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::group([
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => 'admin',
    ], function () {
        Route::get('/dashboard', [DashboardController::class, 'showAdminDashboard'])->name('dashboard');
        Route::resource('/categories', CategoryController::class);
        Route::resource('/products', AdminProductController::class);
        Route::resource('/product-images', ProductImageController::class);
        Route::resource('/staffs', StaffController::class);
    });

    Route::group([
        'prefix' => 'customer',
        'as' => 'customer.',
        'middleware' => 'customer',
    ], function () {
        Route::get('/home', [CustomerPageController::class, 'showHome'])->name('home');
        Route::get('/categories', [CustomerPageController::class, 'showCategories'])->name('categories.index');
        Route::get('/products/{product}', [CustomerPageController::class, 'showProduct'])->name('products.show');
        Route::get('/{category}/products/{id}', [CustomerPageController::class, 'showProductOfCategory'])
            ->name('products');
        Route::resource('/cart-items', CartController::class);
        Route::resource('/orders', CustomerOrderController::class);
        Route::resource('/payment-images', PaymentImageController::class);
        Route::delete('/remove-cart-items', [CartController::class, 'bulkDestroy'])->name('cart-items.bulk-destroy');

        Route::post('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // web.php

    Route::group([
        'prefix' => 'staff',
        'as' => 'staff.',
        'middleware' => 'staff',
    ], function () {
        Route::get('/dashboard', [StaffPageController::class, 'index'])->name('dashboard');

        Route::get('/orders/{status}', [StaffPageController::class, 'showOrders'])->name('orders.status');
        Route::put('/orders/update/{order}', [StaffPageController::class, 'updateStatus'])->name('orders.update');
    });

});
