<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_ke_ban_hang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doanh_nghiep_id')->constrained('doanh_nghiep')->onDelete('cascade');
            $table->decimal('tong_doanh_thu', 15, 2)->default(0);
            $table->integer('so_don_hang')->default(0);
            $table->integer('so_san_pham_ban')->default(0);
            $table->date('thoi_gian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_ke_ban_hang');
    }
};
