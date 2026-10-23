<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi akun user / admin yang dikelola Super Admin.
 */
class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $akun = $this->route('user') ?? $this->route('admin');
        $baru = $akun === null;

        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($akun?->id_user, 'id_user')],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password' => [$baru ? 'required' : 'nullable', 'string', 'min:8'],
        ];
    }
}
