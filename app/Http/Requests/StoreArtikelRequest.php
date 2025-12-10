<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255|unique:artikels,judul',
            'slug' => 'nullable|string|max:255|unique:artikels,slug',
            'konten' => 'required|string',
            'gambar_utama' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'kategori_artikel_id' => 'required|exists:kategori_artikels,id',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul artikel wajib diisi',
            'judul.unique' => 'Judul artikel sudah digunakan',
            'judul.max' => 'Judul artikel maksimal 255 karakter',
            'slug.unique' => 'Slug sudah digunakan',
            'slug.max' => 'Slug maksimal 255 karakter',
            'konten.required' => 'Konten artikel wajib diisi',
            'gambar_utama.image' => 'File harus berupa gambar',
            'gambar_utama.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'gambar_utama.max' => 'Ukuran gambar utama maksimal 2MB',
            'gambar_thumbnail.image' => 'File harus berupa gambar',
            'gambar_thumbnail.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'gambar_thumbnail.max' => 'Ukuran gambar thumbnail maksimal 512KB',
            'meta_title.max' => 'Meta title maksimal 60 karakter',
            'meta_description.max' => 'Meta description maksimal 160 karakter',
            'meta_keywords.max' => 'Meta keywords maksimal 255 karakter',
            'kategori_artikel_id.required' => 'Kategori wajib dipilih',
            'kategori_artikel_id.exists' => 'Kategori tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
            'published_at.date' => 'Format tanggal publish tidak valid',
        ];
    }
}
