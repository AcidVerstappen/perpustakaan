<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Models\Shelf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $books = Book::query()
            ->with(['category', 'shelf'])
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('kode_buku', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $isAdmin = $request->user()->hasAnyRole(['Super Admin', 'Admin Perpustakaan']);

        return view('books.index', compact('books', 'search', 'isAdmin'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $shelves = Shelf::orderBy('kode_rak')->get();

        return view('books.create', compact('categories', 'shelves'));
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        if (! isset($data['stok_tersedia'])) {
            $data['stok_tersedia'] = $data['jumlah_buku'];
        }

        Book::create($data);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book): View
    {
        $book->load(['category', 'shelf']);
        $isAdmin = auth()->user()->hasAnyRole(['Super Admin', 'Admin Perpustakaan']);

        return view('books.show', compact('book', 'isAdmin'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $shelves = Shelf::orderBy('kode_rak')->get();

        return view('books.edit', compact('book', 'categories', 'shelves'));
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
