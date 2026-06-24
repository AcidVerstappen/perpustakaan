@extends('layouts.admin')

@section('title', 'Scan QR Code Buku')
@section('page-title', 'Scan QR Code Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Scan QR</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Arahkan Kamera ke QR Code</h5>
    </div>
    <div class="card-body text-center">
        <div id="qr-reader" style="width: 500px; max-width: 100%; margin: auto;"></div>
        <form id="qr-form" action="{{ route('qr-scan.handle') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="qr_code_url" id="qr-code-url">
        </form>
        <div id="qr-reader-results" class="mt-3"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Code scanned = ${decodedText}`, decodedResult);
        document.getElementById('qr-code-url').value = decodedText;
        document.getElementById('qr-form').submit();
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 }
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endpush
