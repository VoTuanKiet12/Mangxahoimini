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
        Schema::create('binh_luan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bai_viet_id');
            $table->unsignedBigInteger('user_id');
            $table->text('noi_dung')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->timestamp('ngay_binh_luan')->useCurrent();
            $table->foreign('bai_viet_id')->references('id')->on('bai_viet')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luan');
    }
};
