<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'key' => ['required','string','max:255'],
            'value' => ['nullable','string'],
            'description' => ['nullable','string','max:500'],
            'type' => ['nullable','in:text,textarea,number,boolean,file'],
            // optional file field for settings like hero_bg
            'file' => ['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:5120'],
        ];

        // For existing settings, key is not required for updates
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $rules['key'] = ['sometimes','string','max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Kode pengaturan wajib diisi.',
            'key.max' => 'Kode pengaturan maksimal 255 karakter.',
            'value.string' => 'Nilai pengaturan harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'type.in' => 'Tipe data harus salah satu dari: text, textarea, number, boolean, file.',
            'file.image' => 'File harus berupa gambar.',
            'file.mimes' => 'File harus berformat: jpg, jpeg, png, webp, atau svg.',
            'file.max' => 'File maksimal 5MB.',
        ];
    }
}
