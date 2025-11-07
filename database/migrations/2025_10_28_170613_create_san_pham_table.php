<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doanh_nghiep_id')->constrained('doanh_nghiep')->onDelete('cascade');
            $table->string('ten_san_pham');
            $table->text('mo_ta')->nullable();
            $table->json('hinh_anh')->nullable();
            $table->decimal('gia', 12, 2);
            $table->integer('so_luong')->default(0);
            $table->enum('trang_thai', ['con_hang', 'het_hang', 'an'])->default('con_hang');
            $table->timestamp('ngay_dang')->useCurrent();
            $table->timestamps();
            $table->foreignId('loai_id')->nullable()->constrained('loai_san_pham')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('san_pham');
    }
};
