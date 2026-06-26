<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->enum('kondisi_buku', ['baik', 'rusak', 'hilang'])->nullable()->after('total_denda');
            $table->text('catatan_kondisi')->nullable()->after('kondisi_buku');
        });
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn(['kondisi_buku', 'catatan_kondisi']);
        });
    }
};
