<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Super Admin', 'Admin Perpustakaan']);
    }

    public function rules(): array
    {
        $member = $this->route('member');
        $userId = $member?->user_id;

        return [
            'nis' => [
                'required',
                'string',
                'max:30',
                Rule::unique('members', 'nis')->ignore($member),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'buat_akun' => ['nullable', 'boolean'],
            'email' => [
                Rule::requiredIf(fn () => $this->boolean('buat_akun')),
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                Rule::requiredIf(fn () => $this->boolean('buat_akun') && ! $userId),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis' => 'NIS',
            'jenis_kelamin' => 'jenis kelamin',
            'buat_akun' => 'buat akun siswa',
        ];
    }
}
