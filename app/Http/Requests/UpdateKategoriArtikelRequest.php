<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kategoriArtikel = $this->route('kategoriArtikel');
        
        return [
            'nama' => 'required|string|max:255|unique:kategori_artikels,nama,' . $kategoriArtikel->id,
            'slug' => 'nullable|string|max:255|unique:kategori_artikels,slug,' . $kategoriArtikel->id,
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique' => 'Nama kategori sudah digunakan',
            'nama.max' => 'Nama kategori maksimal 255 karakter',
            'slug.unique' => 'Slug sudah digunakan',
            'slug.max' => 'Slug maksimal 255 karakter',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'gambar.max' => 'Ukuran gambar maksimal 2MB',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ];
    }
}
