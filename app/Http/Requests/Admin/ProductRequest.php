<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $encodedId = $this->route('product');
        $id = $encodedId ? decode_id((string) $encodedId) : null;
        $imageRule = $this->isMethod('post') ? 'required' : 'nullable';
        return [
            'store_id' => ['required','integer','exists:stores,id'],
            'name' => ['required','string','max:255'],
            'slug' => ['required','string','max:255','unique:products,slug'.($id?','.$id:'')],
            'price' => ['required','numeric','min:0'],
            'compare_at_price' => ['nullable','numeric','min:0'],
            'unit' => ['nullable','string','max:50'],
            'stock_qty' => ['nullable','integer','min:0'],
            'short_description' => ['nullable','string','max:500'],
            'description' => ['nullable','string'],
            'main_image' => [$imageRule,'image','mimes:jpg,jpeg,png,webp','max:4096'],
            'images' => ['nullable','array'],
            'images.*' => ['image','mimes:jpg,jpeg,png,webp','max:4096'],
            'is_active' => ['nullable'],
            'is_featured' => ['nullable'],
            'is_bestseller' => ['nullable'],
            'category_ids' => ['nullable','array'],
            'category_ids.*' => ['integer','exists:categories,id'],
        ];
    }
}
