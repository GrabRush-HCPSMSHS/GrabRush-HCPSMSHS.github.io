<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function completeDelete(ProductImage $productImage): void
    {
        if (Storage::disk('public')->exists($productImage->path)) {
            Storage::disk('public')->delete($productImage->path);
        }

        $productImage->delete();
    }

    public function assignImage(int $productImageId, int $productId): void
    {
        ProductImage::findOrFail($productImageId)->update(['product_id' => $productId]);
    }
}
