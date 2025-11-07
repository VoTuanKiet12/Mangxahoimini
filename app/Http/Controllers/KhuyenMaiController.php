<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use App\Models\DoanhNghiep;
use App\Models\LoaiSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SanPham;

class KhuyenMaiController extends Controller
{
    // 📜 Danh sách khuyến mãi
    public function index()
    {
        $user = Auth::user();

        if (!$user->doanh_nghiep) {
            return redirect()->back()->with('error', 'Bạn chưa có doanh nghiệp để quản lý khuyến mãi.');
        }

        $doanhNghiepId = $user->doanh_nghiep->id;

        // ✅ Tự động cập nhật trạng thái khi đã hết hạn
        KhuyenMai::where('doanh_nghiep_id', $doanhNghiepId)
            ->where('ngay_ket_thuc', '<', now())
            ->where('trang_thai', 'hoat_dong')
            ->update(['trang_thai' => 'het_han']);

        $khuyenMais = KhuyenMai::where('doanh_nghiep_id', $doanhNghiepId)->get();

        return view('doanhnghiep.khuyenmai.index', compact('khuyenMais'));
    }


    // ➕ Form thêm mới
    public function create()
    {
        $user = Auth::user();
        $doanhNghiepId = $user->doanh_nghiep->id ?? null;


        $loaiSanPham = LoaiSanPham::all();
        $sanPham = SanPham::where('doanh_nghiep_id', $doanhNghiepId)->get();
        return view('doanhnghiep.khuyenmai.create', compact('loaiSanPham', 'sanPham'));
    }

    // 💾 Lưu khuyến mãi mới
    public function store(Request $request)
    {
        $request->validate([
            'ten_khuyen_mai' => 'required|string|max:255',
            'loai_ap_dung'   => 'required|in:san_pham,loai_san_pham',
            'muc_giam'       => 'required|numeric|min:0|max:100',
            'ngay_bat_dau'   => 'required|date',
            'ngay_ket_thuc'  => 'required|date|after:ngay_bat_dau',
        ]);

        $user = Auth::user();
        $doanhNghiep = $user->doanh_nghiep;

        if (!$doanhNghiep) {
            return redirect()->back()->with('error', 'Bạn chưa có doanh nghiệp để thêm khuyến mãi.');
        }

        // Lấy ID tương ứng
        $doiTuongId = $request->loai_ap_dung === 'san_pham'
            ? $request->doi_tuong_sanpham_id
            : $request->doi_tuong_loai_id;

        // Nếu chưa chọn gì
        if (!$doiTuongId) {
            return redirect()->back()->with('error', 'Vui lòng chọn đối tượng áp dụng.');
        }

        KhuyenMai::create([
            'doanh_nghiep_id' => $doanhNghiep->id,
            'ten_khuyen_mai'  => $request->ten_khuyen_mai,
            'loai_ap_dung'    => $request->loai_ap_dung,
            'doi_tuong_id'    => $doiTuongId,
            'muc_giam'        => $request->muc_giam,
            'ngay_bat_dau'    => $request->ngay_bat_dau,
            'ngay_ket_thuc'   => $request->ngay_ket_thuc,
            'trang_thai'      => 'hoat_dong',
        ]);

        return redirect()->route('khuyenmai.index')->with('success', 'Thêm khuyến mãi thành công!');
    }

    // 🧩 Form chỉnh sửa
    public function edit(KhuyenMai $khuyenmai)
    {
        $user = Auth::user();
        $sanPham = SanPham::where('doanh_nghiep_id', $user->doanh_nghiep->id)->get();
        $loaiSanPham = LoaiSanPham::all();
        return view('doanhnghiep.khuyenmai.edit', compact('khuyenmai', 'sanPham', 'loaiSanPham'));
    }


    // 🧩 Cập nhật dữ liệu
    public function update(Request $request, KhuyenMai $khuyenmai)
    {
        $request->validate([
            'ten_khuyen_mai' => 'required|string|max:255',
            'loai_ap_dung'   => 'required|in:san_pham,loai_san_pham',
            'doi_tuong_id'   => 'required|integer',
            'muc_giam'       => 'required|numeric|min:0|max:100',
            'ngay_bat_dau'   => 'required|date',
            'ngay_ket_thuc'  => 'required|date|after:ngay_bat_dau',
        ]);

        $khuyenmai->update([
            'ten_khuyen_mai' => $request->ten_khuyen_mai,
            'loai_ap_dung'   => $request->loai_ap_dung,
            'doi_tuong_id'   => $request->doi_tuong_id,
            'muc_giam'       => $request->muc_giam,
            'ngay_bat_dau'   => $request->ngay_bat_dau,
            'ngay_ket_thuc'  => $request->ngay_ket_thuc,
        ]);

        return redirect()->route('khuyenmai.index')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    // 🗑️ Xóa khuyến mãi
    public function destroy(KhuyenMai $khuyenmai)
    {
        $khuyenmai->delete();
        return redirect()->route('khuyenmai.index')->with('success', 'Đã xóa khuyến mãi!');
    }
}
