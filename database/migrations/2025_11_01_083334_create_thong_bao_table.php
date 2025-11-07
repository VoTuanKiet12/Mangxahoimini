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
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại liên kết đến người dùng nhận thông báo
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Nội dung thông báo
            $table->text('noi_dung');
            $table->string('link')->nullable();
            // Trạng thái đã đọc: 0 = chưa đọc, 1 = đã đọc
            $table->boolean('da_doc')->default(false);

            // Thời gian tạo và cập nhật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
