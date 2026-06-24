<?php
use App\Models\Book;
$f = \Faker\Factory::create('id_ID');
$books = Book::where('judul', 'like', 'Buku Tambahan%')->get();
foreach($books as $b) {
    $judul = ucwords($f->words(3, true));
    $penulis = $f->firstName() . ' ' . $f->lastName();
    $b->update([
        'judul' => $judul,
        'slug' => Book::generateUniqueSlug($judul, $b->id),
        'penulis' => $penulis
    ]);
}
echo 'FIXED';
