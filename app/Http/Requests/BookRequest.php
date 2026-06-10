<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Super Admin', 'Admin Perpustakaan']);
    }

    public function rules(): array
    {
        $book = $this->route('book');

        return [
            'kode_buku' => [
                'required',
                'string',
                'max:30',
                Rule::unique('books', 'kode_buku')->ignore($book),
            ],
            'isbn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($book),
            ],
            'judul' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'shelf_id' => ['required', 'exists:shelves,id'],
            'penulis' => ['required', 'string', 'max:150'],
            'penerbit' => ['nullable', 'string', 'max:150'],
            'tahun_terbit' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'jumlah_buku' => ['required', 'integer', 'min:1'],
            'stok_tersedia' => [
                'required',
                'integer',
                'min:0',
                'lte:jumlah_buku',
            ],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_buku' => 'kode buku',
            'category_id' => 'kategori',
            'shelf_id' => 'rak',
            'jumlah_buku' => 'jumlah buku',
            'stok_tersedia' => 'stok tersedia',
        ];
    }
}
