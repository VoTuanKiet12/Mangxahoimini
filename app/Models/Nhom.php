<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nhom extends Model
{
    use HasFactory;

    protected $table = 'nhom';
    protected $primaryKey = 'id';
    // vì bảng dùng timestamp riêng (ngay_tao)

    protected $fillable = [
        'ten_nhom',
        'mo_ta',
        'anh_bia',
        'nguoi_tao_id',
        'che_do',
        'ngay_tao'
    ];

    /**
     * Người tạo nhóm
     */
    public function chuNhom()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * Danh sách thành viên trong nhóm
     */
    public function thanhVien()
    {
        return $this->hasMany(ThanhVienNhom::class, 'nhom_id');
    }

    /**
     * Thành viên người dùng (thuận tiện cho truy vấn)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'thanh_vien_nhom', 'nhom_id', 'user_id')
            ->withPivot('vai_tro', 'trang_thai', 'ngay_tham_gia');
    }
}
