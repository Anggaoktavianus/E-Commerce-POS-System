<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string','max:500'],
            'icon_class' => ['nullable','string','max:255'],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:2048'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable'],
        ];
    }
}
