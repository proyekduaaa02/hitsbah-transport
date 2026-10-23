<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'id_produk' => ['required', 'integer', 'exists:produk,id_produk'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'komen' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return ['id_produk' => 'produk', 'komen' => 'komentar'];
    }
}
