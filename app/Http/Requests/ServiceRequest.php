<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role !== 'user' && $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:3000'],
            'harga' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'durasi_pemasangan' => ['required', 'integer', 'min:15', 'max:480'],
        ];
    }

    public function attributes(): array
    {
        return ['durasi_pemasangan' => 'durasi pemasangan'];
    }
}
