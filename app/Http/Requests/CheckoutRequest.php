<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'id_services' => ['nullable', 'integer', 'exists:installation_services,id_services'],
            'tanggal' => ['nullable', 'required_with:id_services', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(60)->toDateString()],
            'jam' => ['nullable', 'required_with:id_services', 'date_format:H:i'],
        ];
    }

    public function attributes(): array
    {
        return ['id_services' => 'jasa pemasangan', 'tanggal' => 'tanggal pemasangan', 'jam' => 'jam pemasangan'];
    }
}
