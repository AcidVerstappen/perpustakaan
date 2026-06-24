<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Shelf;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create Categories
        $categories = ['Fiksi', 'Sains', 'Sejarah', 'Teknologi', 'Sastra', 'Biografi', 'Komik', 'Pendidikan'];
        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::firstOrCreate(
                ['nama_kategori' => $cat],
                ['deskripsi' => "Buku kategori $cat"]
            );
            $categoryIds[] = $category->id;
        }

        // Create Shelves
        $shelves = [];
        for ($i = 1; $i <= 5; $i++) {
            $shelf = Shelf::firstOrCreate(
                ['kode_rak' => "R-0$i"],
                ['nama_rak' => "Rak Umum 0$i", 'lokasi' => "Lantai 1 - Lorong $i"]
            );
            $shelves[] = $shelf->id;
        }

        // Create 30 Books
        for ($i = 1; $i <= 30; $i++) {
            $judul = rtrim($faker->sentence(rand(2, 5)), '.');
            $jumlah = rand(2, 10);
            
            $book = Book::firstOrNew(['kode_buku' => 'BK-' . str_pad($i, 4, '0', STR_PAD_LEFT)]);
            
            $book->fill([
                'isbn' => $faker->isbn13(),
                'judul' => $judul,
                'slug' => Str::slug($judul) . '-' . uniqid(),
                'category_id' => $faker->randomElement($categoryIds),
                'shelf_id' => $faker->randomElement($shelves),
                'penulis' => $faker->name(),
                'penerbit' => $faker->company(),
                'tahun_terbit' => rand(2010, 2024),
                'jumlah_buku' => $jumlah,
                'stok_tersedia' => $jumlah,
                'deskripsi' => $faker->paragraph(),
            ]);
            
            $book->save();
        }
    }
}
