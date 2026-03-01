<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $product->reviews()->create(
            array_merge(
                $request->validated(),
                ['user_id' => $request->user()->id]
            )
        );

        return redirect()->route('customer.products.show', $product);
    }

    public function update(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $review->update($request->validated());

        return redirect()->route('customer.products.show', $review->product);
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $product = $review->product;
        $review->delete();

        return redirect()->route('customer.products.show', $product);
    }
}
