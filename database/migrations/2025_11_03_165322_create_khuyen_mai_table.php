<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khuyen_mai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doanh_nghiep_id')
                ->constrained('doanh_nghiep')
                ->onDelete('cascade');
            $table->string('ten_khuyen_mai');
            $table->enum('loai_ap_dung', ['san_pham', 'loai_san_pham'])
                ->default('san_pham');
            $table->decimal('muc_giam', 5, 2)
                ->default(0)
                ->check('muc_giam >= 0 AND muc_giam <= 100');
            $table->unsignedBigInteger('doi_tuong_id');
            $table->dateTime('ngay_bat_dau');
            $table->dateTime('ngay_ket_thuc');
            $table->enum('trang_thai', ['hoat_dong', 'het_han'])
                ->default('hoat_dong');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai');
    }
};
