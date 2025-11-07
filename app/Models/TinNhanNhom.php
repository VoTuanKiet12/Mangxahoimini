<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TinNhanNhom extends Model
{
    use HasFactory;

    protected $table = 'tin_nhan_nhom';
    protected $fillable = ['nhom_id', 'nguoi_gui_id', 'noi_dung', 'anh', 'ngay_gui'];



    public function nguoiGui()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'nhom_id');
    }
}
