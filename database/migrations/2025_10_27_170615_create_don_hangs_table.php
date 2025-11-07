<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('don_hang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doanh_nghiep_id')->nullable()->constrained('doanh_nghiep')->nullOnDelete();
            $table->string('ten_nguoi_nhan')->nullable();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('email_nguoi_nhan')->nullable();
            $table->decimal('tong_tien', 12, 2);
            $table->string('dia_chi_giao');
            $table->enum('trang_thai', ['cho_xac_nhan', 'dang_giao', 'hoan_thanh', 'huy'])->default('cho_xac_nhan');
            $table->timestamp('ngay_dat')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_hang');
    }
};
