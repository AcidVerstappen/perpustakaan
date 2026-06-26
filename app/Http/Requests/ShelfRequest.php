<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShelfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Super Admin');
    }

    public function rules(): array
    {
        return [
            'kode_rak' => [
                'required',
                'string',
                'max:20',
                Rule::unique('shelves', 'kode_rak')->ignore($this->route('shelf')),
            ],
            'nama_rak' => ['required', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_rak' => 'kode rak',
            'nama_rak' => 'nama rak',
        ];
    }
}
