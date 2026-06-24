<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->index('status');
            $table->index('tanggal_jatuh_tempo');
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tanggal_jatuh_tempo']);
            $table->dropIndex(['member_id', 'status']);
        });
    }
};
