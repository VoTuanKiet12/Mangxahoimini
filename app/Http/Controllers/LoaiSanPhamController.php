<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoaiSanPham;

class LoaiSanPhamController extends Controller
{
    // Danh sách loại sản phẩm
    public function index()
    {
        $loaiSanPham = LoaiSanPham::orderBy('id', 'asc')->get();
        return view('admin.loaisanpham', compact('loaiSanPham'));
    }

    // Thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'ten_loai' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:500',
        ]);

        LoaiSanPham::create([
            'ten_loai' => $request->ten_loai,
            'mo_ta' => $request->mo_ta,
        ]);

        return redirect()->route('admin.loaisp.danhsach')->with('success', 'Thêm loại sản phẩm thành công!');
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_loai' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:500',
        ]);

        $loai = LoaiSanPham::findOrFail($id);
        $loai->update($request->only('ten_loai', 'mo_ta'));

        return redirect()->route('admin.loaisp.danhsach')->with('success', 'Cập nhật loại sản phẩm thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $loai = LoaiSanPham::findOrFail($id);
        $loai->delete();

        return redirect()->route('admin.loaisp.danhsach')->with('success', 'Xóa loại sản phẩm thành công!');
    }
}
