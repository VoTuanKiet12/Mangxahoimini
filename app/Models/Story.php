<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $table = 'story';
    protected $primaryKey = 'id';
    public $timestamps = false; // bảng không có created_at, updated_at

    protected $fillable = [
        'user_id',
        'noi_dung',
        'hinh_anh',
        'video',
        'thoi_han'
    ];


    // Story thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
