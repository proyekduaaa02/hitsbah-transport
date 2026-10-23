<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:100'],
            'nama_usaha' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'alamat' => ['required', 'string', 'max:500'],
            'kontak' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i', 'after:jam_buka'],
            'kapasitas_pemasangan' => ['required', 'integer', 'min:1', 'max:20'],
            'info_pembayaran' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'site_name' => 'nama website',
            'nama_usaha' => 'nama usaha',
            'jam_buka' => 'jam buka',
            'jam_tutup' => 'jam tutup',
            'kapasitas_pemasangan' => 'kapasitas pemasangan',
            'info_pembayaran' => 'informasi pembayaran',
        ];
    }
}
