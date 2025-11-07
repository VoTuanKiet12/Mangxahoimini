<?php

namespace App\Http\Controllers;

use App\Models\LuotThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LuotThichController extends Controller
{
    /**
     * Xử lý thêm hoặc đổi cảm xúc cho bài viết.
     */
    public function store(Request $request, $baiVietId)
    {
        // Nếu chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thả cảm xúc.');
        }

        $userId = Auth::id();
        $camXuc = $request->input('cam_xuc', 'like'); // Mặc định là "like"

        // Kiểm tra người này đã thả cảm xúc cho bài viết này chưa
        $existing = LuotThich::where('bai_viet_id', $baiVietId)
            ->where('user_id', $userId)
            ->first();

        $trangThai = '';

        if ($existing) {
            if ($existing->cam_xuc !== $camXuc) {
                // Nếu chọn cảm xúc khác => cập nhật
                $existing->update(['cam_xuc' => $camXuc]);
                $trangThai = 'updated';
            } else {
                // Cùng cảm xúc thì giữ nguyên, không làm gì
                $trangThai = 'nochange';
            }
        } else {
            // Nếu chưa từng thả => tạo mới
            LuotThich::create([
                'bai_viet_id' => $baiVietId,
                'user_id' => $userId,
                'cam_xuc' => $camXuc,
            ]);
            $trangThai = 'added';
        }

        // Trả về AJAX response
        if ($request->ajax()) {
            // Lấy toàn bộ cảm xúc của bài viết
            $tatCa = LuotThich::where('bai_viet_id', $baiVietId)->get();
            $tongCamXuc = $tatCa->count();

            // Nhóm và đếm từng loại cảm xúc
            $demCamXuc = $tatCa->groupBy('cam_xuc')->map->count()->toArray();
            arsort($demCamXuc);
            $top3CamXuc = array_slice($demCamXuc, 0, 3, true);

            return response()->json([
                'success' => true,
                'trang_thai' => $trangThai,
                'cam_xuc' => $camXuc,
                'tong' => $tongCamXuc,
                'top3' => $top3CamXuc,
            ]);
        }

        return back();
    }
    public function destroy($id)
    {
        $like = LuotThich::where('bai_viet_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
        }

        // Trả về JSON nếu là AJAX
        if (request()->ajax()) {
            $tongCamXuc = LuotThich::where('bai_viet_id', $id)->count();
            $demCamXuc = LuotThich::where('bai_viet_id', $id)->get()
                ->groupBy('cam_xuc')->map->count()->toArray();
            arsort($demCamXuc);
            $top3CamXuc = array_slice($demCamXuc, 0, 3, true);

            return response()->json([
                'success' => true,
                'trang_thai' => 'deleted',
                'tong' => $tongCamXuc,
                'top3' => $top3CamXuc,
            ]);
        }

        return back();
    }
}
