<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class KhuyenMai extends Model
{
    use HasFactory;
    protected $table = 'khuyen_mai';
    protected $fillable = [
        'doanh_nghiep_id',
        'ten_khuyen_mai',
        'loai_ap_dung',
        'muc_giam',
        'doi_tuong_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];
    public function doanhNghiep()
    {
        return $this->belongsTo(DoanhNghiep::class, 'doanh_nghiep_id');
    }
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'doi_tuong_id')->where('loai_ap_dung', 'san_pham');
    }
    public function loaiSanPham()
    {
        return $this->belongsTo(LoaiSanPham::class, 'doi_tuong_id')
            ->where('loai_ap_dung', 'loai_san_pham');
    }

    // Kiểm tra mã khuyến mãi còn hiệu lực hay không
    public function getConHieuLucAttribute()
    {
        $now = Carbon::now();
        return $this->trang_thai === 'hoat_dong'
            && $now->between($this->ngay_bat_dau, $this->ngay_ket_thuc);
    }
}
