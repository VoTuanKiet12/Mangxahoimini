<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_hang_id')->constrained('don_hang')->onDelete('cascade');
            $table->enum('phuong_thuc', ['tien_mat', 'chuyen_khoan', 'vi_dien_tu'])->default('tien_mat');
            $table->decimal('so_tien', 12, 2);
            $table->enum('trang_thai', ['cho_thanh_toan', 'da_thanh_toan', 'that_bai'])->default('cho_thanh_toan');
            $table->string('ma_giao_dich', 100)->nullable();
            $table->timestamp('ngay_thanh_toan')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
