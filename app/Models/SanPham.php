<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KhuyenMai;
use Carbon\Carbon;

class SanPham extends Model
{
    use HasFactory;
    protected $table = 'san_pham';
    protected $fillable = [
        'doanh_nghiep_id',
        'ten_san_pham',
        'mo_ta',
        'hinh_anh',
        'gia',
        'so_luong',
        'trang_thai',
        'loai_id',

    ];

    protected $casts = [
        'hinh_anh' => 'array',
    ];

    public function doanhNghiep()
    {
        return $this->belongsTo(DoanhNghiep::class, 'doanh_nghiep_id');
    }

    public function chiTietDonHang()
    {
        return $this->hasMany(ChiTietDonHang::class);
    }
    public function loaiSanPham()
    {
        return $this->belongsTo(LoaiSanPham::class, 'loai_id');
    }
    public function danhGia()
    {
        return $this->hasMany(DanhGiaSanPham::class, 'san_pham_id')
            ->orderBy('created_at', 'desc');
    }
    public function khuyenMaiHienTai()
    {
        return $this->hasOne(KhuyenMai::class, 'doi_tuong_id')
            ->where('loai_ap_dung', 'san_pham')
            ->where('trang_thai', 'hoat_dong')
            ->whereDate('ngay_bat_dau', '<=', now())
            ->whereDate('ngay_ket_thuc', '>=', now());
    }

    // ✅ Giá sau khi giảm
    public function getGiaSauKhuyenMaiAttribute()
    {
        $km = $this->khuyenMaiHienTai()->first();
        return $km ? round($this->gia * (1 - $km->muc_giam / 100), 0) : $this->gia;
    }
    public function getTrungBinhSaoAttribute()
    {
        return $this->danhGia()->avg('so_sao');
    }
    public function capNhatTrangThaiTheoSoLuong()
    {
        if ($this->so_luong <= 0) {
            $this->trang_thai = 'het_hang';
        } elseif ($this->so_luong > 0 && $this->trang_thai !== 'an') {
            $this->trang_thai = 'con_hang';
        }
        $this->save();
    }
    public function bai_viet()
    {
        return $this->hasMany(BaiViet::class, 'san_pham_id');
    }
}
