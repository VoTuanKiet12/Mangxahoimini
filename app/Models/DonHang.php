<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'don_hang';
    protected $fillable = [
        'user_id',
        'doanh_nghiep_id',
        'ten_nguoi_nhan',
        'so_dien_thoai',
        'email_nguoi_nhan',
        'dia_chi_giao',
        'tong_tien',
        'trang_thai',
    ];

    // 🧩 Quan hệ tới người dùng
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 📦 Quan hệ tới chi tiết đơn hàng
    // 📦 Quan hệ tới chi tiết đơn hàng
    public function chiTietDonHang()
    {
        return $this->hasMany(ChiTietDonHang::class, 'don_hang_id');
    }
    public function doanhNghiep()
    {
        return $this->belongsTo(DoanhNghiep::class, 'doanh_nghiep_id');
    }


    // 💰 Quan hệ tới thanh toán
    public function thanhToan()
    {
        return $this->hasOne(ThanhToan::class, 'don_hang_id');
    }
}
