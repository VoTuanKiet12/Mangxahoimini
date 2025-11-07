<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGiaSanPham extends Model
{
    use HasFactory;

    protected $table = 'danh_gia_san_pham';
    protected $fillable = [
        'san_pham_id',
        'user_id',
        'so_sao',
        'noi_dung',
        'hinh_anh',
        'ngay_danh_gia'
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
