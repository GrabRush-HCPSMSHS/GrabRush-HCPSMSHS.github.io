<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => ['required', 'string', 'min:3', 'max:100', Rule::unique('products')->ignore($this->product)],
            'description' => 'required|string|min:10|max:5000',
            'product_image_id' => 'required|integer|exists:product_images,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1.00',
            'is_available' => 'sometimes|in:0,1',
        ];
    }
}
