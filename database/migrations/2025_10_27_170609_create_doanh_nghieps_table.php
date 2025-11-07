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
        Schema::create('doanh_nghiep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ten_cua_hang');
            $table->text('mo_ta')->nullable();
            $table->string('logo')->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->enum('trang_thai', ['cho_duyet', 'hoat_dong', 'tu_choi'])->default('cho_duyet');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doanh_nghiep');
    }
};
