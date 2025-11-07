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
        Schema::create('gio_hang', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại đến người dùng
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Khóa ngoại đến sản phẩm
            $table->foreignId('san_pham_id')
                ->constrained('san_pham')
                ->onDelete('cascade');

            // Số lượng sản phẩm, mặc định = 1
            $table->integer('so_luong')->default(1);

            // Ngày thêm vào giỏ hàng
            $table->timestamp('ngay_them')->useCurrent();

            // Thời gian tạo và cập nhật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gio_hang');
    }
};
