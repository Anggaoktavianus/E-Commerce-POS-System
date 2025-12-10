<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'author_name' => ['required','string','max:255'],
            'author_title' => ['nullable','string','max:255'],
            'content' => ['required','string','max:2000'],
            'rating' => ['nullable','integer','min:1','max:5'],
            'avatar' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable','integer','min:0'],
        ];
    }
}
