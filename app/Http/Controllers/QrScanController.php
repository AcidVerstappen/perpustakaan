<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QrScanController extends Controller
{
    public function __construct(protected \App\Services\QRCodeService $qrCodeService)
    {
    }

    public function index()
    {
        return view('qr-scan.index');
    }

    public function handle(Request $request)
    {
        $request->validate(['qr_code_url' => 'required']);

        try {
            $result = $this->qrCodeService->handleScan($request->qr_code_url);
            
            if ($result['type'] === 'borrowing') {
                return redirect()->route('borrowings.show', $result['model'])->with('success', 'Booking ditemukan: ' . $result['model']->kode_pinjam);
            }
            
            if ($result['type'] === 'book') {
                return redirect()->route('books.show', $result['model'])->with('success', 'Buku ditemukan: ' . $result['model']->judul);
            }
        } catch (\App\Exceptions\QRCodeNotFoundException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
