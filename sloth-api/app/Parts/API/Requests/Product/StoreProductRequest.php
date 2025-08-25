<?php

namespace App\Parts\API\Requests\Product;

use App\DTOs\Product\ProductDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'description' => 'nullable|string',
            'preview_image' => 'nullable|string',
            'price' => 'nullable|numeric',
            'price_latest' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'main_category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
        ];
    }

    protected function toDTO(): ProductDTO
    {
        return ProductDTO::fromArray(
            [
                'title' => $this->input('title'),
                'slug' => $this->input('slug'),
                'description' => $this->input('description'),
                'preview_image' => $this->input('preview_image'),
                'price' => $this->input('price'),
                'price_latest' => $this->input('price_latest'),
                'discount' => $this->input('discount'),
                'main_category_id' => $this->input('main_category_id'),
                'images' => $this->input('images'),
            ]
        );
    }

}
