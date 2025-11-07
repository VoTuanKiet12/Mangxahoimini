<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuotThich extends Model
{
    use HasFactory;

    protected $table = 'luot_thich';
    protected $primaryKey = 'id';
    // bảng không có created_at, updated_at

    protected $fillable = [
        'user_id',
        'bai_viet_id',
        'cam_xuc',
        'ngay_thich',
    ];

    // Quan hệ: Lượt thích thuộc về 1 bài viết
    public function baiViet()
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id', 'id');
    }

    // Quan hệ: Lượt thích thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
