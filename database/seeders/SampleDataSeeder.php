<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $fiction = Category::firstOrCreate(
            ['nama_kategori' => 'Fiksi'],
            ['deskripsi' => 'Buku cerita fiksi dan novel']
        );

        $science = Category::firstOrCreate(
            ['nama_kategori' => 'Sains'],
            ['deskripsi' => 'Buku pengetahuan dan sains']
        );

        $rakA = Shelf::firstOrCreate(
            ['kode_rak' => 'A-01'],
            ['nama_rak' => 'Rak Fiksi A', 'lokasi' => 'Lantai 1 - Barat']
        );

        $rakB = Shelf::firstOrCreate(
            ['kode_rak' => 'B-01'],
            ['nama_rak' => 'Rak Sains B', 'lokasi' => 'Lantai 1 - Timur']
        );

        $books = [
            [
                'kode_buku' => 'BK-001',
                'isbn' => '9786020321234',
                'judul' => 'Laskar Pelangi',
                'category_id' => $fiction->id,
                'shelf_id' => $rakA->id,
                'penulis' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'tahun_terbit' => 2005,
                'jumlah_buku' => 5,
                'stok_tersedia' => 5,
                'deskripsi' => 'Novel inspiratif tentang perjuangan siswa di Belitung.',
            ],
            [
                'kode_buku' => 'BK-002',
                'isbn' => '9789790734567',
                'judul' => 'Bumi',
                'category_id' => $fiction->id,
                'shelf_id' => $rakA->id,
                'penulis' => 'Tere Liye',
                'penerbit' => 'Republika',
                'tahun_terbit' => 2014,
                'jumlah_buku' => 3,
                'stok_tersedia' => 2,
                'deskripsi' => 'Seri novel petualangan dunia paralel.',
            ],
            [
                'kode_buku' => 'BK-003',
                'isbn' => '9789790798765',
                'judul' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'category_id' => $science->id,
                'shelf_id' => $rakB->id,
                'penulis' => 'Yuval Noah Harari',
                'penerbit' => 'Gramedia',
                'tahun_terbit' => 2017,
                'jumlah_buku' => 2,
                'stok_tersedia' => 1,
                'deskripsi' => 'Buku sejarah evolusi manusia dari perspektif unik.',
            ],
        ];

        foreach ($books as $data) {
            $book = Book::firstOrNew(['kode_buku' => $data['kode_buku']]);
            $data['slug'] = Book::generateUniqueSlug($data['judul'], $book->id);
            $book->fill($data);
            $book->save();
        }
    }
}
