<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShelfRequest;
use App\Models\Shelf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShelfController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $shelves = Shelf::query()
            ->withCount('books')
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('kode_rak', 'like', "%{$search}%")
                        ->orWhere('nama_rak', 'like', "%{$search}%")
                        ->orWhere('lokasi', 'like', "%{$search}%");
                });
            })
            ->orderBy('kode_rak')
            ->paginate(10)
            ->withQueryString();

        return view('shelves.index', compact('shelves', 'search'));
    }

    public function create(): View
    {
        return view('shelves.create');
    }

    public function store(ShelfRequest $request): RedirectResponse
    {
        Shelf::create($request->validated());

        return redirect()
            ->route('shelves.index')
            ->with('success', 'Rak berhasil ditambahkan.');
    }

    public function edit(Shelf $shelf): View
    {
        return view('shelves.edit', compact('shelf'));
    }

    public function update(ShelfRequest $request, Shelf $shelf): RedirectResponse
    {
        $shelf->update($request->validated());

        return redirect()
            ->route('shelves.index')
            ->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Shelf $shelf): RedirectResponse
    {
        if ($shelf->books()->exists()) {
            return back()->with('error', 'Rak tidak dapat dihapus karena masih digunakan oleh buku.');
        }

        $shelf->delete();

        return redirect()
            ->route('shelves.index')
            ->with('success', 'Rak berhasil dihapus.');
    }
}
