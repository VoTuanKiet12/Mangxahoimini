<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVienNhom extends Model
{
    use HasFactory;

    protected $table = 'thanh_vien_nhom';
    protected $primaryKey = 'id';


    protected $fillable = [
        'nhom_id',
        'user_id',
        'vai_tro',
        'trang_thai',
        'ngay_tham_gia'
    ];

    /**
     * Liên kết đến nhóm
     */
    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }

    /**
     * Liên kết đến người dùng
     */
    public function nguoiGui()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }
}
