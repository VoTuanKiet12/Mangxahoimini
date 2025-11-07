<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoanhNghiep extends Model
{
    use HasFactory;
    protected $table = 'doanh_nghiep';
    protected $fillable = [
        'user_id',
        'ten_cua_hang',
        'mo_ta',
        'logo',
        'dia_chi',
        'so_dien_thoai',
        'trang_thai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'doanh_nghiep_id', 'id');
    }

    public function thongKe()
    {
        return $this->hasMany(ThongKeBanHang::class, 'doanh_nghiep_id');
    }
}
