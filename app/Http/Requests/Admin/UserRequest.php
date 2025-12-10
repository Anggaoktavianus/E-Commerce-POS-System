<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? null;

        $passwordRules = $this->isMethod('post')
            ? ['required','string','min:8']
            : ['nullable','string','min:8'];

        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,'.($userId ?? 'NULL').',id'],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:500'],
            'role' => ['nullable','in:admin,mitra,customer'],
            'password' => $passwordRules,
            'is_verified' => ['nullable'],
            // location fields: required for mitra/customer, optional for admin
            'loc_provinsi_id' => ['required_unless:role,admin','integer','in:31,32,33,34,35','exists:loc_provinsis,id'],
            'loc_kabkota_id' => ['required_unless:role,admin','integer','exists:loc_kabkotas,id'],
            'loc_kecamatan_id' => ['required_unless:role,admin','integer','exists:loc_kecamatans,id'],
            'loc_desa_id' => ['required_unless:role,admin','integer','exists:loc_desas,id'],
        ];
    }
}
