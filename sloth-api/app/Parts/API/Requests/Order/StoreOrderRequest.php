<?php

namespace App\Parts\API\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'currency' => 'nullable|string|size:3',
            'subtotal' => 'nullable|integer|min:0',
            'discount' => 'nullable|integer|min:0',
            'total' => 'nullable|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'guest_token' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'sometimes|integer|exists:products,id',
            'items.*.quantity' => 'sometimes|integer|min:1',
            'meta' => ['nullable', 'array'],
        ];
    }
}
