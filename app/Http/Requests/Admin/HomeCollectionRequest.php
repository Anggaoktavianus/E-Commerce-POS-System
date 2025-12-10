<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeCollectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('collection');
        return [
            'name' => ['required','string','max:255'],
            'key' => ['required','string','max:255','unique:home_collections,key'.($id?','.$id:'')],
            'description' => ['nullable','string','max:1000'],
            'is_active' => ['nullable'],
        ];
    }
}
