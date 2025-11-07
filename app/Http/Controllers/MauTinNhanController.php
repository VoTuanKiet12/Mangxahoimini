<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MauTinNhan;

class MauTinNhanController extends Controller
{
    /**
     * 🟢 Lấy màu giữa mình và bạn bè
     */
    public function layMau($friendId)
    {
        $userId = Auth::id();
        [$me, $friend] = $this->sapXepCap($userId, $friendId);

        $record = MauTinNhan::where('user_minh_id', $me)
            ->where('ban_be_id', $friend)
            ->first();

        return response()->json([
            'color' => $record->mau ?? '#0084ff',
            'background' => $record->anh_nen ?? null,
        ]);
    }

    /**
     * 🔵 Lưu hoặc cập nhật màu chat
     */
    public function luuMau(Request $request)
    {
        $userId = Auth::id();
        $friendId = $request->input('friend_id');
        $color = $request->input('color', '#0084ff');

        [$me, $friend] = $this->sapXepCap($userId, $friendId);

        MauTinNhan::updateOrCreate(
            ['user_minh_id' => $me, 'ban_be_id' => $friend],
            ['mau' => $color]
        );

        return response()->json(['success' => true]);
    }

    /**
     * 🧩 Đảm bảo (mình, bạn) luôn theo thứ tự tăng
     */
    private function sapXepCap($a, $b)
    {
        return ($a < $b) ? [$a, $b] : [$b, $a];
    }

    /**
     * 🟣 Lưu ảnh nền chat
     */
    public function luuAnhNen(Request $request)
    {
        $request->validate([
            'ban_be_id' => 'required|integer',
            'anh_nen' => 'required|string',
        ]);

        $userId = Auth::id();
        $friendId = $request->ban_be_id;
        [$me, $friend] = $this->sapXepCap($userId, $friendId);

        $mau = MauTinNhan::updateOrCreate(
            ['user_minh_id' => $me, 'ban_be_id' => $friend],
            ['anh_nen' => $request->anh_nen]
        );

        return response()->json(['success' => true]);
    }

    /**
     * 🔴 Xóa ảnh nền chat
     */
    public function xoaAnhNen(Request $request)
    {
        $userId = Auth::id();
        $banBeId = $request->input('ban_be_id');

        [$me, $friend] = $this->sapXepCap($userId, $banBeId);

        $record = MauTinNhan::where('user_minh_id', $me)
            ->where('ban_be_id', $friend)
            ->first();

        if ($record) {
            $record->anh_nen = null;
            $record->save();

            return response()->json(['success' => true, 'message' => 'Đã xóa ảnh nền.']);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi.']);
    }
}
