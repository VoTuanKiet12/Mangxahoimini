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
        Schema::create('bai_viet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('noi_dung')->nullable();

            // Lưu nhiều ảnh (mảng JSON)
            $table->json('hinh_anh')->nullable();

            // Mỗi bài viết 1 video (nếu có)
            $table->string('video')->nullable();

            $table->timestamp('ngay_dang')->useCurrent();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('san_pham_id')
                ->references('id')
                ->on('san_pham')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_viet');
    }
};
