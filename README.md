# Perpustakaan Digital

Perpustakaan Digital adalah aplikasi web berbasis Laravel yang dibuat untuk membantu pengelolaan data perpustakaan, mulai dari data buku, anggota, peminjaman, hingga pengembalian buku.

Project ini dibuat sebagai aplikasi pembelajaran fullstack development dengan struktur Laravel yang sederhana, rapi, dan mudah dikembangkan. Tujuan utama project ini adalah menjadi referensi praktis bagi mahasiswa atau beginner developer yang ingin memahami alur dasar pembuatan aplikasi web menggunakan Laravel.

## Fitur

* Dashboard admin
* Manajemen data buku
* Manajemen data anggota
* Peminjaman buku
* Pengembalian buku
* Database migration
* Validasi form
* Tampilan web sederhana dan responsif

## Tech Stack

* Laravel
* PHP
* Blade Template
* SQLite / MySQL
* Vite
* Bootstrap / CSS

## Cara Menjalankan Project

Clone repository:

```bash
git clone https://github.com/AcidVerstappen/perpustakaan.git
cd perpustakaan
```

Install dependency Laravel:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Jalankan migration:

```bash
php artisan migrate
```

Jalankan server Laravel:

```bash
php artisan serve
```

Jalankan Vite:

```bash
npm run dev
```

## Struktur Project

```text
app/          Logic utama aplikasi Laravel
database/     Migration dan seeder database
resources/    View Blade, CSS, dan asset frontend
routes/       Route web aplikasi
public/       File publik aplikasi
```

## Status Project

Project ini masih dalam tahap pengembangan. Beberapa bagian akan terus diperbaiki, seperti fitur, tampilan, dokumentasi, validasi, dan struktur kode.

## Tujuan Project

Project ini dibuat sebagai project pembelajaran dan referensi open-source sederhana untuk memahami bagaimana aplikasi web perpustakaan dibangun menggunakan Laravel.

## License

Project ini bersifat open-source dan dapat digunakan sebagai referensi pembelajaran.
