<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            // Kita tambah kolom user_id setelah kolom id
            // nullable() artinya boleh kosong dulu (untuk data lama)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            // Kita sambungkan ke tabel users
            // onDelete('cascade') artinya kalau user dihapus, lokasinya juga ikut terhapus
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            // Hapus foreign key dulu, baru kolomnya
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};