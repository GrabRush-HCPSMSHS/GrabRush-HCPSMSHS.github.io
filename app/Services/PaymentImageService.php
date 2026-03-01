<?php

namespace App\Services;

use App\Models\PaymentImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentImageService
{
    public function createImage(UploadedFile $image, int $userId): PaymentImage
    {
        return PaymentImage::create([
            'path' => $image->store('payment-images', 'public_uploads'),
            'user_id' => $userId,
        ]);
    }

    public function assignImageToOrder(int $paymentImageId, int $orderId): void
    {
        PaymentImage::findOrFail($paymentImageId)->update(['order_id' => $orderId]);
    }

    public function completeDelete(PaymentImage $paymentImage): void
    {
        if (Storage::disk('public_uploads')->exists($paymentImage->path)) {
            Storage::disk('public_uploads')->delete($paymentImage->path);
        }

        $paymentImage->delete();
    }
}
