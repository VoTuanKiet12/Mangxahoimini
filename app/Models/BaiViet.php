<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiViet extends Model
{
    use HasFactory;

    protected $table = 'bai_viet';
    protected $primaryKey = 'id'; // mặc định Laravel cũng dùng id
    // vì bảng không có created_at, updated_at

    protected $fillable = [
        'user_id',
        'noi_dung',
        'hinh_anh',
        'video',
        'ngay_dang',
        'san_pham_id',
    ];
    protected $casts = [
        'hinh_anh' => 'array',
    ];

    // Quan hệ: Bài viết thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Quan hệ: 1 bài viết có nhiều bình luận
    public function binhLuan()
    {
        return $this->hasMany(BinhLuan::class, 'bai_viet_id', 'id');
    }

    // Quan hệ: 1 bài viết có nhiều lượt thích
    public function luotThich()
    {
        return $this->hasMany(LuotThich::class, 'bai_viet_id', 'id');
    }
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}
