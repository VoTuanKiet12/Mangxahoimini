<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongKeBanHang extends Model
{
    use HasFactory;

    protected $table = 'thong_ke_ban_hang';
    protected $fillable = [
        'doanh_nghiep_id',
        'tong_doanh_thu',
        'so_don_hang',
        'so_san_pham_ban',
        'thoi_gian'
    ];

    public function doanhNghiep()
    {
        return $this->belongsTo(DoanhNghiep::class, 'doanh_nghiep_id');
    }
}
