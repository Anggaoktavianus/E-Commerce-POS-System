<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('category');
        return [
            'name' => ['required','string','max:255'],
            'slug' => ['required','string','max:255','unique:categories,slug'.($id?','.$id:'')],
            'parent_id' => ['nullable','integer','exists:categories,id'],
            'is_active' => ['nullable'],
        ];
    }
}
