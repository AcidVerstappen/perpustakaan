<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminLibrary();
    }

    public function rules(): array
    {
        return [
            'tanggal_kembali' => ['nullable', 'date'],
            'items' => ['required', 'array'],
            'items.*.qty' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasQty = collect($this->input('items', []))
                ->contains(fn ($item) => (int) ($item['qty'] ?? 0) > 0);

            if (! $hasQty) {
                $validator->errors()->add('items', 'Isi jumlah kembali minimal 1 eksemplar.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'items.*.qty' => 'jumlah dikembalikan',
        ];
    }
}
