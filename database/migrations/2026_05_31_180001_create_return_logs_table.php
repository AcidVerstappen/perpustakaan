<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_kembali');
            $table->unsignedInteger('total_qty_kembali');
            $table->text('ringkasan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_logs');
    }
};
