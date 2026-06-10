<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->unsignedInteger('qty_dikembalikan')->default(0)->after('qty');
        });

        DB::statement(
            'UPDATE borrowing_details bd
             INNER JOIN borrowings b ON b.id = bd.borrowing_id
             SET bd.qty_dikembalikan = bd.qty
             WHERE b.status = ?',
            ['selesai']
        );
    }

    public function down(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->dropColumn('qty_dikembalikan');
        });
    }
};
