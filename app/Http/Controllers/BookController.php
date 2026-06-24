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
    public function __construct(protected \App\Services\BookService $bookService)
    {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $books = $this->bookService->getAllBooks($search);
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
        $this->bookService->createBook($request->validated() + ['cover' => $request->file('cover')]);

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
        $this->bookService->updateBook($book, $request->validated() + ['cover' => $request->file('cover')]);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->bookService->deleteBook($book);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
