<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Book;
use App\Services\BorrowingService;


class BookBookingController extends Controller
{
    public function __construct(protected BorrowingService $borrowingService)
    {
    }

    public function cart(Request $request)
    {
        $cart = $request->session()->get('booking_cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return view('bookings.cart', compact('cart', 'books'));
    }

    public function add(Request $request, Book $book)
    {
        if ($book->stok_tersedia <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $cart = $request->session()->get('booking_cart', []);
        $cart[$book->id] = ($cart[$book->id] ?? 0) + 1;

        if ($cart[$book->id] > $book->stok_tersedia) {
            $cart[$book->id] = $book->stok_tersedia;
            return back()->with('warning', 'Jumlah buku di keranjang melebihi stok yang tersedia.');
        }

        $request->session()->put('booking_cart', $cart);

        return back()->with('success', 'Buku ditambahkan ke keranjang.');
    }

    public function remove(Request $request, Book $book)
    {
        $cart = $request->session()->get('booking_cart', []);
        unset($cart[$book->id]);
        $request->session()->put('booking_cart', $cart);

        return back()->with('success', 'Buku dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $cart = $request->session()->get('booking_cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang booking kosong.');
        }

        $items = [];
        foreach($cart as $bookId => $qty) {
            $items[] = ['book_id' => $bookId, 'qty' => $qty];
        }

        $this->borrowingService->create($request->user()->member, $items);

        $request->session()->forget('booking_cart');

        return redirect()->route('borrowings.index')->with('success', 'Booking berhasil dibuat. Silakan ambil buku di perpustakaan sebelum batas waktu pengambilan.');
    }
}
