<?php

namespace App\Services;

use App\Exceptions\QRCodeNotFoundException;
use App\Models\Book;
use App\Models\Borrowing;

class QRCodeService
{
    public function handleScan(string $code)
    {
        $code = trim($code);

        if ($code === '') {
            throw new QRCodeNotFoundException();
        }

        // 1. Exact match: kode_pinjam (e.g. "PJ-20260625-0001")
        $borrowing = Borrowing::where('kode_pinjam', $code)->first();
        if ($borrowing) {
            return ['type' => 'borrowing', 'model' => $borrowing];
        }

        // 2. Regex match: extract book ID from any URL/path containing /books/{id}
        //    Backward compatible:
        //      http://localhost/books/5          → id=5  ✓  (generator output)
        //      https://perpus.test/books/5/qr     → id=5  ✓  (old assumed format)
        //      /books/5                           → id=5  ✓  (relative path)
        //      /books/5?foo=bar                   → id=5  ✓  (query string)
        //      /books/5#section                   → id=5  ✓  (fragment)
        //      /books/abc                         → no match (non-numeric)
        if (preg_match('#/books/(\d+)(?:/.*)?$#', $code, $matches)) {
            $book = Book::find((int) $matches[1]);
            if ($book) {
                return ['type' => 'book', 'model' => $book];
            }
        }

        // 3. Fallback: bare numeric ID ("5", "123")
        if (ctype_digit($code)) {
            $book = Book::find((int) $code);
            if ($book) {
                return ['type' => 'book', 'model' => $book];
            }
        }

        throw new QRCodeNotFoundException();
    }
}
