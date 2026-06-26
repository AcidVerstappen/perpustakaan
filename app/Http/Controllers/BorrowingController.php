<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    public function __construct(
        protected BorrowingService $borrowingService
    ) {}

    public function index(Request $request): View
    {
        Cache::remember('borrowings:mark-overdue:last-run', now()->addMinute(), function () {
            $this->borrowingService->markOverdueBorrowings();
            return true;
        });

        $search = $request->string('search')->trim();
        $status = $request->string('status')->toString();
        $user = $request->user();

        $borrowings = $this->borrowingService->getBorrowings($search, $status, $user);

        $noMember = false;
        if ($user->hasRole('Siswa')) {
            $noMember = !$user->member;
        }

        $isStaff = $user->isAdminLibrary() || $user->isPetugas();

        return view('borrowings.index', [
            'borrowings' => $borrowings,
            'search' => $search,
            'status' => $status,
            'isAdmin' => $isStaff,
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
            'isAdmin' => auth()->user()->isAdminLibrary() || auth()->user()->isPetugas(),
        ]);
    }

    public function qr(Borrowing $borrowing)
    {
        $this->authorizeBorrowingAccess($borrowing);

        // Generate default base64 data URI
        $qrCodeDataUri = (new \chillerlan\QRCode\QRCode)->render($borrowing->kode_pinjam);
        
        // Extract base64 string and decode to raw SVG
        $base64 = substr($qrCodeDataUri, strpos($qrCodeDataUri, ',') + 1);
        $rawSvg = base64_decode($base64);

        return response($rawSvg, 200)->header('Content-Type', 'image/svg+xml');
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
        try {
            $this->borrowingService->delete($borrowing);
            return redirect()
                ->route('borrowings.index')
                ->with('success', 'Peminjaman berhasil dihapus.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function authorizeBorrowingAccess(Borrowing $borrowing): void
    {
        $user = auth()->user();

        if ($user->isAdminLibrary() || $user->isPetugas()) {
            return;
        }

        if ($user->hasRole('Siswa') && $user->member?->id === $borrowing->member_id) {
            return;
        }

        abort(403);
    }
}
