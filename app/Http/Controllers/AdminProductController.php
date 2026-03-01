<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

class AdminProductController extends Controller
{
    public function __construct(protected ProductImageService $productImageService, protected ProductRepository $productRepository, protected CategoryRepository $categoryRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filterCategoryId = $request->string('filterCategoryId');
        $category = null;
        if ($filterCategoryId) {
            $category = Category::find($filterCategoryId);
        }

        $products = $this->productRepository->findAll(
            $request->string('filterProductName'),
            $category,
            $request->input('filterProductStatus') !== null ? $request->boolean('filterProductStatus') : null
        );

        $categories = $this->categoryRepository->findAll();

        return inertia()->render('Admin/Product/ProductsRecord', [
            'products' => inertia()->merge(fn () => $products->items()),
            'products_pagination' => $products->toArray(),
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());
        $productImageId = $request->integer('product_image_id');
        $this->productImageService->assignImage($productImageId, $product->id);

        return redirect()->route('admin.products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): Response
    {
        $product->load(['image', 'category', 'reviews.user']);

        Gate::authorize('view', $product);

        $categories = $this->categoryRepository->findAll();

        return inertia('Admin/Product/ProductShow', [
            'product' => $product,
            'categories' => $categories,
            'reviews' => $product->reviews,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        $oldProductImageId = $request->integer('old_product_image_id');
        $newProductImageId = $request->integer('product_image_id');

        if ($oldProductImageId !== $newProductImageId) {
            $this->productImageService->completeDelete(ProductImage::findOrFail($oldProductImageId));
            $this->productImageService->assignImage($newProductImageId, $product->id);
        }

        return redirect()->route('admin.products.show', $product->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $this->productImageService->completeDelete(ProductImage::whereBelongsTo($product)->first());

        $product->delete();

        return redirect()->route('admin.products.index');
    }
}
