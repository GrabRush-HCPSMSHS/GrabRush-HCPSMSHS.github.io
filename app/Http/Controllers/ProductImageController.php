<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProductImageController extends ImageController
{
    protected string $folder = 'product-images';

    public function store(StoreImageRequest $request): int
    {
        return $this->storeImage($request->file('product-image'), new ProductImage);
    }

    public function destroy(ProductImage $productImage): void
    {
        Gate::authorize('delete', $productImage);

        try {
            $this->deleteImage($productImage->id, new ProductImage);
        } catch (\Exception $e) {
            Log::error('Error deleting product image: '.$e->getMessage());
        }
    }
}
