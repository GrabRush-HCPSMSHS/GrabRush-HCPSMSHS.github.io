<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->routeIs('customer.reviews.store')) {
            return $this->user()->role === UserRole::Customer;
        }

        if ($this->routeIs('customer.reviews.update')) {
            return Gate::allows('update', $this->route('review'));
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ];

        if ($this->routeIs('reviews.store')) {
            $rules['product_id'] = ['required', 'exists:products,id'];
        }

        return $rules;
    }
}
