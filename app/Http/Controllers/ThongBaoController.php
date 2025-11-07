<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    public function danhDauDaDoc()
    {
        if (Auth::check()) {
            ThongBao::where('user_id', Auth::id())
                ->where('da_doc', 0)
                ->update(['da_doc' => 1]);
        }

        return response()->json(['success' => true]);
    }
}
