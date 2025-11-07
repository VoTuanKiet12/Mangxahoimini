<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;

    protected $table = 'binh_luan';
    protected $primaryKey = 'id';
    // bảng không có created_at, updated_at

    protected $fillable = [
        'bai_viet_id',
        'user_id',
        'noi_dung',
        'ngay_binh_luan',
        'hinh_anh'
    ];

    // Bình luận thuộc về 1 bài viết
    public function baiViet()
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id', 'id');
    }

    // Bình luận thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
