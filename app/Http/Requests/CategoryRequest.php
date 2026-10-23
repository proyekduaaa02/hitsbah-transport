<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $kategori = $this->route('category');

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategori', 'nama')->ignore($kategori?->id_kategori, 'id_kategori')],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
