<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShelfRequest;
use App\Models\Shelf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShelfController extends Controller
{
    public function __construct(protected \App\Services\ShelfService $shelfService)
    {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $shelves = $this->shelfService->getAllShelves($search);

        return view('shelves.index', compact('shelves', 'search'));
    }

    public function create(): View
    {
        return view('shelves.create');
    }

    public function store(ShelfRequest $request): RedirectResponse
    {
        $this->shelfService->createShelf($request->validated());

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
        $this->shelfService->updateShelf($shelf, $request->validated());

        return redirect()
            ->route('shelves.index')
            ->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Shelf $shelf): RedirectResponse
    {
        try {
            $this->shelfService->deleteShelf($shelf);
            return redirect()
                ->route('shelves.index')
                ->with('success', 'Rak berhasil dihapus.');
        } catch (\App\Exceptions\ShelfInUseException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
