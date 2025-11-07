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
        Schema::create('mau_tin_nhan', function (Blueprint $table) {
            $table->id();

            // Người dùng hiện tại (mình)
            $table->foreignId('user_minh_id')->constrained('users')->onDelete('cascade');

            // Người bạn trong cuộc trò chuyện
            $table->foreignId('ban_be_id')->constrained('users')->onDelete('cascade');

            // Màu chung của cuộc trò chuyện
            $table->string('mau', 10)->default('#0084ff');

            // Cập nhật tự động khi đổi màu
            $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

            // Đảm bảo mỗi cặp (mình, bạn) chỉ có 1 dòng
            $table->unique(['user_minh_id', 'ban_be_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mau_tin_nhan');
    }
};
