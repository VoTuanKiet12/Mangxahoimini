<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\DanhGiaSanPham;

class DanhGiaSanPhamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'san_pham_id' => 'required|exists:san_pham,id',
            'so_sao' => 'required|integer|min:1|max:5',
            'noi_dung' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('danhgia', 'public');
        }

        DanhGiaSanPham::create([
            'user_id' => Auth::id(),
            'san_pham_id' => $request->san_pham_id,
            'so_sao' => $request->so_sao,
            'noi_dung' => $request->noi_dung,
            'hinh_anh' => $path,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
    public function destroy($id)
    {
        $danhGia = DanhGiaSanPham::findOrFail($id);

        // Chỉ người tạo hoặc admin được xóa
        if (Auth::id() !== $danhGia->user_id && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Bạn không có quyền xóa đánh giá này.');
        }

        // Xóa file ảnh (nếu có)
        if ($danhGia->hinh_anh && Storage::disk('public')->exists($danhGia->hinh_anh)) {
            Storage::disk('public')->delete($danhGia->hinh_anh);
        }

        $danhGia->delete();

        return back()->with('success', 'Đánh giá đã được xóa thành công.');
    }
}
