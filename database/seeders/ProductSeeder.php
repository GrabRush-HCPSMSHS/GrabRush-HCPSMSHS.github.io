<?php

namespace Database\Seeders;

use App\Http\Controllers\ProductImageController;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class ProductSeeder extends Seeder
{
    protected string $folder = 'product-images';

    public function __construct(protected ProductImageService $productImageService) {}

    public function run(): void
    {
        $categories = Category::all();
        $imageController = new ProductImageController;

        foreach ($categories as $category) {
            $products = Product::factory()->count(3)->make(['category_id' => $category->id]);
            $createdProducts = $category->products()->createMany($products->toArray());

            foreach ($createdProducts as $product) {
                $imageIndex = rand(1, 24);
                $imagePath = public_path("sample/{$imageIndex}.png");
                $image = new UploadedFile($imagePath, "{$imageIndex}.png");
                $imageId = $imageController->storeImage($image, new ProductImage);
                $this->productImageService->assignImage($imageId, $product->id);
            }
        }
    }
}
