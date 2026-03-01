<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === UserRole::Customer;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_image_id' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('payment_method') === PaymentMethod::UploadReceipt->value),
                'integer',
                Rule::exists('payment_images', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
            'payment_method' => ['required', 'string', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('payment_image_id') === '') {
            $this->merge([
                'payment_image_id' => null,
            ]);
        }
    }
}
