<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'id_services' => ['required', 'integer', 'exists:installation_services,id_services'],
            'tanggal' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(60)->toDateString()],
            'jam' => ['required', 'date_format:H:i'],
        ];
    }

    public function attributes(): array
    {
        return ['id_services' => 'jasa pemasangan', 'tanggal' => 'tanggal pemasangan', 'jam' => 'jam pemasangan'];
    }
}
