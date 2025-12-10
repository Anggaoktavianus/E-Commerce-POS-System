<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialLinkRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'platform' => ['required','string','max:255'],
            'icon_class' => ['nullable','string','max:255'],
            'url' => ['required','url','max:500'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable'],
        ];
    }
}
