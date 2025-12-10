<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable','string','max:255'],
            'subtitle' => ['nullable','string','max:255'],
            'button_text' => ['nullable','string','max:100'],
            'button_url' => ['nullable','string'],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:4096'],
            'position' => ['required','in:home_top,home_middle,home_bottom'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable'],
            'show_circle' => ['nullable'],
            'circle_number' => ['nullable','string','max:10'],
            'circle_value' => ['nullable','string','max:20'],
            'circle_unit' => ['nullable','string','max:10'],
        ];
    }
}
