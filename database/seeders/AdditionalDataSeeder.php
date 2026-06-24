<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdditionalDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        
        $category = Category::first();
        $shelf = Shelf::first();
        
        // Add books until we have 18 total
        $currentBooks = Book::count();
        for ($i = $currentBooks + 1; $i <= 18; $i++) {
            $judul = ucwords($faker->words(3, true));
            Book::create([
                'kode_buku' => 'BK-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'isbn' => $faker->isbn13(),
                'judul' => ucwords($judul),
                'slug' => Book::generateUniqueSlug($judul),
                'category_id' => $category->id,
                'shelf_id' => $shelf->id,
                'penulis' => $faker->firstName() . ' ' . $faker->lastName(),
                'penerbit' => $faker->company(),
                'tahun_terbit' => $faker->numberBetween(2000, 2026),
                'jumlah_buku' => 5,
                'stok_tersedia' => 5,
                'deskripsi' => $faker->paragraph(),
            ]);
        }
        
        // Add members until we have 18 total
        $currentMembers = Member::count();
        for ($i = $currentMembers + 1; $i <= 18; $i++) {
            $name = $faker->firstName() . ' ' . $faker->lastName();
            $user = User::create([
                'name' => $name,
                'email' => 'siswa' . $i . '@perpus.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $user->syncRoles(['Siswa']);
            
            Member::create([
                'nis' => '202600' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'nama' => $name,
                'kelas' => $faker->randomElement(['X', 'XI', 'XII']) . ' ' . 
                           $faker->randomElement(['IPA', 'IPS']) . ' ' . 
                           $faker->numberBetween(1, 5),
                'jurusan' => $faker->randomElement(['IPA', 'IPS']),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'alamat' => $faker->address(),
                'no_hp' => $faker->phoneNumber(),
            ]);
        }
    }
}
