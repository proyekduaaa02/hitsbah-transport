<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'id_kategori' => ['required', 'integer', 'exists:kategori,id_kategori'],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'harga' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'stok' => ['required', 'integer', 'min:0', 'max:1000000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['id_kategori' => 'kategori'];
    }
}
