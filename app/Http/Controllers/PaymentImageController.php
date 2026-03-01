<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Models\PaymentImage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PaymentImageController extends ImageController
{
    protected string $folder = 'payment-images';

    public function store(StoreImageRequest $request): int
    {
        return $this->storeImage($request->file('payment-image'), new PaymentImage);
    }

    public function destroy(PaymentImage $paymentImage): void
    {
        Gate::authorize('delete', $paymentImage);

        try {
            $this->deleteImage($paymentImage->id, new PaymentImage);
        } catch (\Exception $e) {
            Log::error('Error deleting payment image: '.$e->getMessage());
        }
    }
}
