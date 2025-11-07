<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MauTinNhan extends Model
{
    use HasFactory;

    protected $table = 'mau_tin_nhan';
    protected $fillable = ['user_minh_id', 'ban_be_id', 'mau', 'anh_nen'];

    public $timestamps = false;

    // Người dùng (mình)
    public function userMinh()
    {
        return $this->belongsTo(User::class, 'user_minh_id');
    }

    // Người bạn
    public function banBe()
    {
        return $this->belongsTo(User::class, 'ban_be_id');
    }
}
