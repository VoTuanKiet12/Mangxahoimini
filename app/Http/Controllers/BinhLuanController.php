<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BinhLuan;
use App\Models\BaiViet;

class BinhLuanController extends Controller
{
    // 🟢 Lấy danh sách bình luận cho 1 bài viết (AJAX)
    public function index($postId)
    {
        $post = BaiViet::findOrFail($postId);

        $comments = BinhLuan::with('user')
            ->where('bai_viet_id', $postId)
            ->latest('ngay_binh_luan')
            ->get();

        return view('comments.list', compact('post', 'comments'));
    }

    // 🟢 Thêm mới bình luận (văn bản + ảnh)
    public function store(Request $request)
    {
        $request->validate([
            'post_id'   => 'required|exists:bai_viet,id',
            'noi_dung'  => 'nullable|string|max:1000',
            'hinh_anh'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        // 🔸 Nếu không có nội dung và không có ảnh => báo lỗi
        if (!$request->noi_dung && !$request->hasFile('hinh_anh')) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận trống!'
            ]);
        }

        // 🔹 Lưu ảnh nếu có
        $path = null;
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('binhluan', 'public');
        }

        // 🔹 Lưu bình luận
        $comment = BinhLuan::create([
            'bai_viet_id'    => $request->post_id,
            'user_id'        => auth()->id(),
            'noi_dung'       => $request->noi_dung,
            'hinh_anh'       => $path,
            'ngay_binh_luan' => now(),
        ]);

        // 🔹 Lấy lại bình luận có user (để render)
        $newComment = BinhLuan::with('user')->find($comment->id);

        $html = view('comments.item', ['cmt' => $newComment])->render();

        return response()->json([
            'success' => true,
            'html'    => $html
        ]);
    }

    // 🗑️ Xóa bình luận
    public function destroy($id)
    {
        $binhLuan = BinhLuan::find($id);

        if (!$binhLuan) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận không tồn tại.'
            ], 404);
        }

        // Kiểm tra quyền
        if (auth()->id() !== $binhLuan->user_id && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bình luận này.'
            ], 403);
        }

        // Xóa file ảnh nếu có
        if ($binhLuan->hinh_anh && \Storage::disk('public')->exists($binhLuan->hinh_anh)) {
            \Storage::disk('public')->delete($binhLuan->hinh_anh);
        }

        $binhLuan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bình luận.'
        ]);
    }
}
