<?php

namespace App\Http\Requests;

use App\Models\CartItem;
use App\Services\ProductService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class StoreCartItemRequest extends FormRequest
{
    public function __construct(protected ProductService $productService) {}

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->routeIs('customer.cart-items.update')) {
            return Gate::allows('update', $this->route('cart_item'));
        }

        return Gate::allows('create', CartItem::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Validate the request after default validation rules.
     *
     * @throws ValidationException
     */
    protected function passedValidation(): void
    {
        $product = $this->productService->findOneById($this->product_id);

        if (! $product || ! $product->is_available) {
            throw ValidationException::withMessages([
                'product_id' => 'The selected product is not available.',
            ]);
        }

        if ($this->quantity > $product->quantity) {
            throw ValidationException::withMessages([
                'quantity' => "The selected quantity exceeds the available stock ({$product->quantity}).",
            ]);
        }
    }
}
