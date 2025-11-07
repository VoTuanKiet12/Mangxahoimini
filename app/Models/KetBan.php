<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetBan extends Model
{
    protected $table = 'ket_ban';
    // bảng không có created_at, updated_at

    protected $fillable = [
        'user_id',
        'ban_be_id',
        'ngay_ket_ban',
        'trang_thai'
    ];

    // Người gửi lời mời
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Người nhận lời mời
    public function banBe()
    {
        return $this->belongsTo(User::class, 'ban_be_id');
    }
}
