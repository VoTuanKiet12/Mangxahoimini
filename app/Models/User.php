<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Các cột cho phép gán giá trị hàng loạt (mass assignment).
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'role',
        'anh_dai_dien',
        'anh_bia',
        'dia_chi',
        'so_dien_thoai',
        'ngay_sinh',
        'trang_thai',
        'password',
    ];

    /**
     * Các cột sẽ bị ẩn khi xuất ra JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kiểu dữ liệu khi cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ngay_sinh' => 'date', // tự động cast thành Carbon
        ];
    }

    /**
     * Quan hệ: Một user có nhiều bài viết.
     */
    public function baiViets()
    {
        return $this->hasMany(BaiViet::class, 'user_id', 'id');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'user_id', 'id');
    }

    public function tinNhanGui()
    {
        return $this->hasMany(TinNhan::class, 'nguoi_gui_id', 'id');
    }

    public function tinNhanNhan()
    {
        return $this->hasMany(TinNhan::class, 'nguoi_nhan_id', 'id');
    }

    public function stories()
    {
        return $this->hasMany(Story::class, 'user_id', 'id');
    }

    public function ketBanGui()
    {
        return $this->hasMany(KetBan::class, 'user_id', 'id');
    }

    public function ketBanNhan()
    {
        return $this->hasMany(KetBan::class, 'ban_be_id', 'id');
    }

    public function luotThiches()
    {
        return $this->hasMany(LuotThich::class, 'user_id', 'id');
    }

    public function nhomDaTao()
    {
        return $this->hasMany(Nhom::class, 'nguoi_tao_id');
    }
    public function doanh_nghiep()
    {
        return $this->hasOne(DoanhNghiep::class, 'user_id');
    }

    public function nhom()
    {
        return $this->belongsToMany(Nhom::class, 'thanh_vien_nhom', 'user_id', 'nhom_id')
            ->withPivot('vai_tro', 'trang_thai', 'ngay_tham_gia');
    }
}
