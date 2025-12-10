<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FactRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'label' => ['required','string','max:255'],
            'value' => ['required','integer','min:0'],
            'icon_class' => ['nullable','string','max:255'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable','integer','min:0'],
        ];
    }
}
