<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->hasFile('payment-image')) {
            return $this->user()->role === UserRole::Customer;
        }

        if ($this->hasFile('product-image')) {
            return $this->user()->role === UserRole::Admin;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'payment-image' => 'nullable|image|mimes:jpeg,png|max:2048|dimensions:max_width=5000,max_height=5000',
            'product-image' => 'nullable|image|mimes:jpeg,png|max:2048|dimensions:max_width=5000,max_height=5000',
        ];
    }

    public function messages(): array
    {
        return [
            'payment-image.required' => 'An image is required.',
            'payment-image.image' => 'The file must be an image.',
            'payment-image.mimes' => 'Only JPEG and PNG formats are allowed.',
            'payment-image.max' => 'The image size must not exceed 2MB.',
            'payment-image.dimensions' => "Don't even think about it: Max dimensions are 5000x5000 pixels.",

            'product-image.required' => 'An image is required.',
            'product-image.image' => 'The file must be an image.',
            'product-image.mimes' => 'Only JPEG and PNG formats are allowed.',
            'product-image.max' => 'The image size must not exceed 2MB.',
            'product-image.dimensions' => "Don't even think about it: Max dimensions are 5000x5000 pixels.",
        ];
    }
}
