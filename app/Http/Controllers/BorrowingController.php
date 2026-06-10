<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    public function __construct(
        protected BorrowingService $borrowingService
    ) {}

    public function index(Request $request): View
    {
        $this->borrowingService->markOverdueBorrowings();

        $search = $request->string('search')->trim();
        $status = $request->string('status')->toString();

        $query = Borrowing::query()
            ->with(['member', 'processor', 'details.book'])
            ->withCount('details')
            ->latest();

        $noMember = false;
        if ($request->user()->hasRole('Siswa')) {
            $member = $request->user()->member;
            if (! $member) {
                $noMember = true;
                $query->whereRaw('0 = 1');
            } else {
                $query->where('member_id', $member->id);
            }
        }

        $query->when($search->isNotEmpty(), function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('kode_pinjam', 'like', "%{$search}%")
                    ->orWhereHas('member', fn ($m) => $m->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%"));
            });
        });

        $query->when($status !== '', fn ($q) => $q->where('status', $status));

        $borrowings = $query->paginate(10)->withQueryString();

        return view('borrowings.index', [
            'borrowings' => $borrowings,
            'search' => $search,
            'status' => $status,
            'isAdmin' => $request->user()->isAdminLibrary(),
            'noMember' => $noMember,
        ]);
    }

    public function create(): View
    {
        $members = Member::orderBy('nama')->get();
        $books = Book::where('stok_tersedia', '>', 0)->orderBy('judul')->get();

        return view('borrowings.create', compact('members', 'books'));
    }

    public function store(BorrowingRequest $request): RedirectResponse
    {
        $member = Member::findOrFail($request->member_id);

        $this->borrowingService->create(
            $member,
            $request->input('books'),
            $request->user()
        );

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil diajukan. Silakan setujui untuk mengurangi stok.');
    }

    public function show(Borrowing $borrowing): View
    {
        $this->authorizeBorrowingAccess($borrowing);

        $borrowing->load(['member', 'processor', 'details.book', 'bookReturn.receiver', 'fine', 'returnLogs.receiver']);

        return view('borrowings.show', [
            'borrowing' => $borrowing,
            'isAdmin' => auth()->user()->isAdminLibrary(),
        ]);
    }

    public function approve(Borrowing $borrowing): RedirectResponse
    {
        $this->borrowingService->approve($borrowing, auth()->user());

        return back()->with('success', 'Peminjaman disetujui. Stok buku telah dikurangi.');
    }

    public function reject(Borrowing $borrowing): RedirectResponse
    {
        $this->borrowingService->reject($borrowing, auth()->user());

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function destroy(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'diajukan') {
            return back()->with('error', 'Hanya peminjaman berstatus diajukan yang dapat dihapus.');
        }

        $borrowing->delete();

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil dihapus.');
    }

    protected function authorizeBorrowingAccess(Borrowing $borrowing): void
    {
        $user = auth()->user();

        if ($user->isAdminLibrary()) {
            return;
        }

        if ($user->hasRole('Siswa') && $user->member?->id === $borrowing->member_id) {
            return;
        }

        abort(403);
    }
}
