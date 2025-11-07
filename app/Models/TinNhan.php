<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TinNhan extends Model
{
    use HasFactory;

    protected $table = 'tin_nhan';
    protected $primaryKey = 'id';
    // bảng không có created_at, updated_at

    protected $fillable = [
        'nguoi_gui_id',
        'nguoi_nhan_id',
        'noi_dung',
        'da_doc',
        'ngay_gui',
        'hinh_anh',
    ];

    // Quan hệ: người gửi tin nhắn
    public function nguoiGui()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id', 'id');
    }

    // Quan hệ: người nhận tin nhắn
    public function nguoiNhan()
    {
        return $this->belongsTo(User::class, 'nguoi_nhan_id', 'id');
    }
}
