<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CarouselSlideRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['nullable','string','max:255'],
            'subtitle' => ['nullable','string','max:255'],
            'button_text' => ['nullable','string','max:100'],
            'button_url' => ['nullable','url'],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:4096'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable'],
        ];
    }
}
