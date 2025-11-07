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
        Schema::create('luot_thich', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bai_viet_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('cam_xuc', ['like', 'love', 'haha', 'wow', 'sad', 'angry'])->default('like');
            $table->timestamp('ngay_thich')->useCurrent();
            $table->unique(['bai_viet_id', 'user_id']);
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
        Schema::dropIfExists('luot_thich');
    }
};
