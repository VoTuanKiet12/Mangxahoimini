<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhom', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nhom');
            $table->text('mo_ta')->nullable();
            $table->string('anh_bia')->nullable();
            $table->foreignId('nguoi_tao_id')->constrained('users')->onDelete('cascade');
            $table->enum('che_do', ['cong_khai', 'kin'])->default('cong_khai');
            $table->timestamp('ngay_tao')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhom');
    }
};
