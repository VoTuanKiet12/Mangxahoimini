<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;

    protected $table = 'gio_hang';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'san_pham_id',
        'so_luong',
        'ngay_them',
    ];

    protected $dates = ['ngay_them', 'created_at', 'updated_at'];

    /**
     * 🧑 Người dùng sở hữu giỏ hàng
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🛒 Sản phẩm trong giỏ hàng
     */
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}
