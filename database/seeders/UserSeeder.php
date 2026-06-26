<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@perpus.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);

        $petugas = User::updateOrCreate(
            ['email' => 'petugas@perpus.test'],
            [
                'name' => 'Petugas',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $petugas->syncRoles(['Petugas']);

        $siswa = User::updateOrCreate(
            ['email' => 'siswa@perpus.test'],
            [
                'name' => 'Siswa Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $siswa->syncRoles(['Siswa']);

        Member::updateOrCreate(
            ['nis' => '20260001'],
            [
                'user_id' => $siswa->id,
                'nama' => 'Siswa Demo',
                'kelas' => 'XII IPA 1',
                'jurusan' => 'IPA',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Contoh No. 1',
                'no_hp' => '081234567890',
            ]
        );
    }
}
