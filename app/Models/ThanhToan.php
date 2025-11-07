<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';
    protected $fillable = [
        'don_hang_id',
        'phuong_thuc',
        'so_tien',
        'trang_thai',
        'ma_giao_dich',
        'ngay_thanh_toan'
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }
}
