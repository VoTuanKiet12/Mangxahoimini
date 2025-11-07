<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiSanPham extends Model
{
    use HasFactory;

    protected $table = 'loai_san_pham';

    protected $fillable = [
        'ten_loai',
        'mo_ta',
    ];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'loai_id');
    }
}
