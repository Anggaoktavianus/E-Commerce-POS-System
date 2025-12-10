<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuLinkRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'label' => ['required','string','max:255'],
            'url' => ['nullable','string','max:500'],
            'route_name' => ['nullable','string','max:255'],
            'page_id' => ['nullable','integer','exists:pages,id'],
            'target' => ['nullable','in:_self,_blank'],
            'parent_id' => ['nullable','integer','exists:navigation_links,id'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable'],
        ];
    }
}
