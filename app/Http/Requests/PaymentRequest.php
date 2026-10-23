<?php

namespace App\Http\Requests;

use App\Models\Pembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'metode' => ['required', Rule::in(array_keys(Pembayaran::METODE))],
            'bukti_pembayaran' => [
                Rule::requiredIf(fn () => $this->input('metode') !== 'tunai'),
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072',
            ],
        ];
    }

    public function attributes(): array
    {
        return ['metode' => 'metode pembayaran', 'bukti_pembayaran' => 'bukti pembayaran'];
    }
}
