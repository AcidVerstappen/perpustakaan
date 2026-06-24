<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Book;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Response;

class BookQrController extends Controller
{
    public function show(Book $book)
    {
        $qrCodeData = route('books.show', $book, true);
        
        $qrCodeDataUri = (new \chillerlan\QRCode\QRCode)->render($qrCodeData);

        // Extract base64 string and decode to raw SVG
        $base64 = substr($qrCodeDataUri, strpos($qrCodeDataUri, ',') + 1);
        $rawSvg = base64_decode($base64);

        return response($rawSvg, 200)->header('Content-Type', 'image/svg+xml');
    }
}
