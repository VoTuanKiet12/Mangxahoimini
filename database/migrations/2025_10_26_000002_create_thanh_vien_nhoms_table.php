<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanh_vien_nhom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhom_id')->constrained('nhom')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('vai_tro', ['chu_nhom', 'quan_tri_vien', 'thanh_vien'])->default('thanh_vien');
            $table->enum('trang_thai', ['cho_duyet', 'tham_gia', 'bi_chan'])->default('tham_gia');
            $table->timestamp('ngay_tham_gia')->useCurrent();

            $table->unique(['nhom_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_vien_nhom');
    }
};
