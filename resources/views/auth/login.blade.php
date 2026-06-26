<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan') }} - Login</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="login-body">
    <div class="login-split">
        {{-- LEFT PANEL --}}
        <div class="login-left">
            <div class="login-left-content">
                <div class="login-illustration">
                    <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="80" y="200" width="240" height="8" rx="2" fill="rgba(255,255,255,0.25)"/>
                        <rect x="80" y="130" width="240" height="8" rx="2" fill="rgba(255,255,255,0.25)"/>
                        <rect x="80" y="130" width="8" height="78" fill="rgba(255,255,255,0.15)"/>
                        <rect x="312" y="130" width="8" height="78" fill="rgba(255,255,255,0.15)"/>
                        <rect x="100" y="145" width="18" height="55" rx="2" fill="rgba(255,255,255,0.7)"/>
                        <rect x="122" y="152" width="14" height="48" rx="2" fill="rgba(255,255,255,0.5)"/>
                        <rect x="140" y="148" width="18" height="52" rx="2" fill="rgba(255,255,255,0.85)"/>
                        <rect x="162" y="155" width="12" height="45" rx="2" fill="rgba(255,255,255,0.4)"/>
                        <rect x="178" y="145" width="16" height="55" rx="2" fill="rgba(255,255,255,0.65)"/>
                        <rect x="198" y="150" width="20" height="50" rx="2" fill="rgba(255,255,255,0.55)"/>
                        <rect x="222" y="148" width="14" height="52" rx="2" fill="rgba(255,255,255,0.75)"/>
                        <rect x="240" y="155" width="18" height="45" rx="2" fill="rgba(255,255,255,0.45)"/>
                        <rect x="262" y="145" width="16" height="55" rx="2" fill="rgba(255,255,255,0.6)"/>
                        <rect x="282" y="150" width="14" height="50" rx="2" fill="rgba(255,255,255,0.5)"/>
                        <rect x="105" y="215" width="20" height="50" rx="2" fill="rgba(255,255,255,0.6)"/>
                        <rect x="129" y="220" width="14" height="45" rx="2" fill="rgba(255,255,255,0.45)"/>
                        <rect x="147" y="212" width="18" height="53" rx="2" fill="rgba(255,255,255,0.75)"/>
                        <rect x="169" y="218" width="16" height="47" rx="2" fill="rgba(255,255,255,0.55)"/>
                        <g transform="translate(210,215)">
                            <path d="M0 35L30 30 30 0Q15 5 0 0Z" fill="rgba(255,255,255,0.8)"/>
                            <path d="M60 35L30 30 30 0Q45 5 60 0Z" fill="rgba(255,255,255,0.65)"/>
                            <line x1="8" y1="10" x2="25" y2="8" stroke="rgba(25,135,84,0.3)" stroke-width="1.5"/>
                            <line x1="8" y1="16" x2="25" y2="14" stroke="rgba(25,135,84,0.3)" stroke-width="1.5"/>
                            <line x1="8" y1="22" x2="25" y2="20" stroke="rgba(25,135,84,0.3)" stroke-width="1.5"/>
                            <line x1="35" y1="8" x2="52" y2="10" stroke="rgba(25,135,84,0.3)" stroke-width="1.5"/>
                            <line x1="35" y1="14" x2="52" y2="16" stroke="rgba(25,135,84,0.3)" stroke-width="1.5"/>
                        </g>
                        <g transform="translate(290,205)">
                            <rect x="8" y="45" width="24" height="4" rx="2" fill="rgba(255,255,255,0.4)"/>
                            <rect x="18" y="15" width="4" height="30" fill="rgba(255,255,255,0.35)"/>
                            <path d="M8 15L20 0 32 15Z" fill="rgba(255,255,255,0.7)"/>
                            <ellipse cx="20" cy="18" rx="8" ry="3" fill="rgba(255,200,50,0.3)"/>
                        </g>
                        <g transform="translate(100,60)">
                            <rect x="12" y="40" width="26" height="25" rx="3" fill="rgba(255,255,255,0.35)"/>
                            <rect x="8" y="38" width="34" height="5" rx="2" fill="rgba(255,255,255,0.4)"/>
                            <path d="M25 38Q25 25 18 18" stroke="rgba(255,255,255,0.7)" stroke-width="2" fill="none"/>
                            <path d="M25 38Q28 22 35 15" stroke="rgba(255,255,255,0.7)" stroke-width="2" fill="none"/>
                            <path d="M25 38Q20 28 10 25" stroke="rgba(255,255,255,0.6)" stroke-width="2" fill="none"/>
                            <ellipse cx="18" cy="16" rx="6" ry="8" fill="rgba(255,255,255,0.3)" transform="rotate(-20 18 16)"/>
                            <ellipse cx="35" cy="13" rx="6" ry="8" fill="rgba(255,255,255,0.35)" transform="rotate(15 35 13)"/>
                            <ellipse cx="10" cy="23" rx="5" ry="7" fill="rgba(255,255,255,0.25)" transform="rotate(-30 10 23)"/>
                        </g>
                        <g transform="translate(170,65)">
                            <rect x="0" y="0" width="60" height="40" rx="3" fill="rgba(255,255,255,0.6)"/>
                            <rect x="4" y="4" width="52" height="30" rx="1" fill="rgba(25,135,84,0.3)"/>
                            <rect x="-5" y="40" width="70" height="5" rx="2" fill="rgba(255,255,255,0.45)"/>
                            <rect x="10" y="10" width="28" height="2" rx="1" fill="rgba(255,255,255,0.6)"/>
                            <rect x="10" y="16" width="38" height="2" rx="1" fill="rgba(255,255,255,0.5)"/>
                            <rect x="10" y="22" width="22" height="2" rx="1" fill="rgba(255,255,255,0.4)"/>
                        </g>
                        <g transform="translate(260,85)">
                            <rect x="0" y="8" width="20" height="22" rx="3" fill="rgba(255,255,255,0.45)"/>
                            <path d="M20 14Q30 14 30 22Q30 30 20 30" stroke="rgba(255,255,255,0.4)" stroke-width="2" fill="none"/>
                            <path d="M5 8Q5 2 10 2Q15 2 15 8" stroke="rgba(255,255,255,0.25)" stroke-width="1.5" fill="none"/>
                        </g>
                        <circle cx="50" cy="50" r="3" fill="rgba(255,255,255,0.15)"/>
                        <circle cx="350" cy="80" r="4" fill="rgba(255,255,255,0.1)"/>
                        <circle cx="370" cy="250" r="2.5" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="40" cy="240" r="3.5" fill="rgba(255,255,255,0.1)"/>
                        <circle cx="200" cy="30" r="2" fill="rgba(255,255,255,0.15)"/>
                    </svg>
                </div>
                <div class="login-brand-icon"><i class="bi bi-book-half"></i></div>
                <h1 class="login-brand-title">Sistem Informasi Perpustakaan</h1>
                <p class="login-brand-desc">Kelola peminjaman dan koleksi buku dengan mudah</p>
            </div>
            <div class="login-deco login-deco-1"></div>
            <div class="login-deco login-deco-2"></div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="login-right">
            <div class="login-form-wrapper">
                <div class="login-mobile-header">
                    <i class="bi bi-book-half text-success fs-2"></i>
                    <h4 class="fw-bold mt-2 mb-1">Sistem Informasi Perpustakaan</h4>
                    <p class="text-muted small mb-0">Kelola peminjaman dan koleksi buku dengan mudah</p>
                </div>
                <div class="login-form-header">
                    <h2 class="fw-bold mb-1">Selamat Datang</h2>
                    <p class="text-muted mb-0">Masuk ke akun Anda untuk melanjutkan</p>
                </div>
                @include('partials.admin.alerts')
                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nama@perpus.test" required autofocus autocomplete="username">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                            <button type="button" class="input-group-text toggle-password" onclick="togglePassword()" aria-label="Tampilkan password">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                            <label class="form-check-label small" for="remember_me">Ingat saya</label>
                        </div>
                        <a href="https://wa.me/6285782280974?text={{ urlencode('Halo Admin Perpustakaan, saya ingin meminta bantuan untuk reset password akun saya.') }}" target="_blank" rel="noopener noreferrer" class="login-forgot-link small">
                            <i class="bi bi-whatsapp"></i> Lupa password? Hubungi Admin
                        </a>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg login-btn">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </button>
                    </div>
                </form>
                <div class="login-footer">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
    <script>
    function togglePassword(){var p=document.getElementById("password"),i=document.getElementById("togglePasswordIcon");if(p.type==="password"){p.type="text";i.classList.replace("bi-eye","bi-eye-slash")}else{p.type="password";i.classList.replace("bi-eye-slash","bi-eye")}}
    </script>
</body>
</html>
