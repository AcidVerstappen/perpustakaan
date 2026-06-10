<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminLibrary();
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'books' => ['required', 'array', 'min:1'],
            'books.*.book_id' => ['required', 'exists:books,id', 'distinct'],
            'books.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'member_id' => 'anggota',
            'books' => 'daftar buku',
            'books.*.book_id' => 'buku',
            'books.*.qty' => 'jumlah',
        ];
    }
}
