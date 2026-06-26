<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminLibrary() || $this->user()->isPetugas();
    }

    public function rules(): array
    {
        $isPetugas = $this->user()->isPetugas();

        $rules = [
            'tanggal_kembali' => ['nullable', 'date'],
            'items' => ['required', 'array'],
            'items.*.qty' => ['nullable', 'integer', 'min:0'],
        ];

        if ($isPetugas) {
            $rules['kondisi_buku'] = ['required', 'in:baik,rusak,hilang'];
            $rules['catatan_kondisi'] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
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
            'kondisi_buku' => 'kondisi buku',
            'catatan_kondisi' => 'catatan kondisi',
        ];
    }
}
