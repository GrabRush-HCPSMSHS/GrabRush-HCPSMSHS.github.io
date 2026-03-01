<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\PaymentImage;
use App\Models\User;
use App\Services\PaymentImageService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class OrderSeeder extends Seeder
{
    public function __construct(protected PaymentImageService $paymentImageService) {}

    public function run(): void
    {
        $customers = User::where('role', UserRole::Customer)->get();

        foreach ($customers as $customer) {
            $orders = Order::factory()->count(2)->make([
                'user_id' => $customer->id,
            ]);
            $createdOrders = $customer->orders()->createMany($orders->toArray());

            foreach ($createdOrders as $order) {
                $imageIndex = rand(1, 24);
                $imagePath = public_path("sample/{$imageIndex}.png");
                $image = new UploadedFile($imagePath, "{$imageIndex}.png");
                $paymentImage = $this->paymentImageService->createImage($image, $customer->id);
                $this->paymentImageService->assignImageToOrder($paymentImage->id, $order->id);
            }
        }
    }
}
