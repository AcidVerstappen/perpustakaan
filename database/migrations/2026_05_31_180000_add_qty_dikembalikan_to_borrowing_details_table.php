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

        DB::table('borrowing_details')
            ->whereIn('borrowing_id', function ($query) {
                $query->select('id')
                    ->from('borrowings')
                    ->where('status', 'selesai');
            })
            ->update(['qty_dikembalikan' => DB::raw('qty')]);
    }

    public function down(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->dropColumn('qty_dikembalikan');
        });
    }
};
