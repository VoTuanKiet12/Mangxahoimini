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
        Schema::create('tin_nhan_nhom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nhom_id');
            $table->unsignedBigInteger('nguoi_gui_id');
            $table->text('noi_dung')->nullable();
            $table->string('anh')->nullable();
            $table->timestamp('ngay_gui')->useCurrent();
            $table->timestamps();
            // Khóa ngoại
            $table->foreign('nhom_id')
                ->references('id')->on('nhom')
                ->onDelete('cascade');

            $table->foreign('nguoi_gui_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tin_nhan_nhom');
    }
};
