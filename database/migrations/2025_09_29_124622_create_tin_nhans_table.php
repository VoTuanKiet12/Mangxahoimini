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
        Schema::create('tin_nhan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nguoi_gui_id');
            $table->unsignedBigInteger('nguoi_nhan_id');
            $table->text('noi_dung')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->boolean('da_doc')->default(false);
            $table->timestamp('ngay_gui')->useCurrent();
            $table->foreign('nguoi_gui_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('nguoi_nhan_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tin_nhan');
    }
};
