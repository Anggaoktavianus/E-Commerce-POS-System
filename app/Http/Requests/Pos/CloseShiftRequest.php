<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class CloseShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCloseShift();
    }

    public function rules(): array
    {
        return [
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'actual_cash.required' => 'Actual cash harus diisi',
            'actual_cash.min' => 'Actual cash tidak boleh negatif',
        ];
    }
}
