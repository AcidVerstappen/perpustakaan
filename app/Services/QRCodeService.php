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

        // Check if it's a kode_pinjam (Booking transaction)
        $borrowing = Borrowing::where('kode_pinjam', $code)->first();
        if ($borrowing) {
            return ['type' => 'borrowing', 'model' => $borrowing];
        }

        // Check if it's a Book QR URL
        $urlPath = parse_url($code, PHP_URL_PATH);
        if ($urlPath) {
            $segments = explode('/', trim($urlPath, '/'));
            
            if (count($segments) === 3 && $segments[0] === 'books' && $segments[2] === 'qr') {
                $bookId = $segments[1];
                $book = Book::find($bookId);

                if ($book) {
                    return ['type' => 'book', 'model' => $book];
                }
            }
        }

        throw new QRCodeNotFoundException();
    }
}
