<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimkiemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->input('q')); // loại bỏ khoảng trắng thừa
        $users = collect();

        if (!empty($keyword)) {
            $authId = Auth::id();

            $users = User::where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('username', 'LIKE', "%{$keyword}%");
            })
                ->where('id', '!=', $authId)
                ->distinct() // tránh trùng lặp
                ->get()
                ->map(function ($user) use ($authId) {
                    // Kiểm tra quan hệ kết bạn
                    $relation = DB::table('ket_ban')
                        ->where(function ($q) use ($authId, $user) {
                            $q->where('user_id', $authId)
                                ->where('ban_be_id', $user->id);
                        })
                        ->orWhere(function ($q) use ($authId, $user) {
                            $q->where('user_id', $user->id)
                                ->where('ban_be_id', $authId);
                        })
                        ->first();

                    // Thêm trạng thái bạn bè vào user
                    $user->friend_status = $relation->trang_thai ?? null;
                    return $user;
                });
        }

        return view('timkiem.index', compact('users', 'keyword'));
    }
}
