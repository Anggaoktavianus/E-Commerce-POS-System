<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class OpenShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canAccessPos();
    }

    public function rules(): array
    {
        return [
            'outlet_id' => 'required|exists:outlets,id',
            'opening_balance' => 'required|numeric|min:0',
            'shift_number' => 'required|in:1,2,3',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'outlet_id.required' => 'Outlet harus dipilih',
            'opening_balance.required' => 'Opening balance harus diisi',
            'opening_balance.min' => 'Opening balance tidak boleh negatif',
            'shift_number.required' => 'Shift number harus dipilih',
            'shift_number.in' => 'Shift number harus 1 (pagi), 2 (siang), atau 3 (malam)',
        ];
    }
}
