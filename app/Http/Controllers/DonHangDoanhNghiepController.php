<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Support\Facades\Auth;
use App\Models\ThongKeBanHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonHangDoanhNghiepController extends Controller
{
    // 🧾 Danh sách đơn hàng thuộc doanh nghiệp đang đăng nhập
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'doanh_nghiep') {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        // Lấy đơn hàng theo doanh nghiệp của user
        $donHangs = DonHang::where('doanh_nghiep_id', $user->id)
            ->with(['chiTietDonHang.sanPham', 'thanhToan'])
            ->orderBy('ngay_dat', 'desc')
            ->get();

        return view('doanhnghiep.donhang.index', compact('donHangs'));
    }

    // 📋 Chi tiết đơn hàng
    public function show($id)
    {
        $user = Auth::user();

        $donHang = DonHang::where('doanh_nghiep_id', $user->id)
            ->with(['chiTietDonHang.sanPham', 'thanhToan'])
            ->findOrFail($id);

        return view('doanhnghiep.donhang.show', compact('donHang'));
    }

    // 🚚 Cập nhật trạng thái đơn hàng
    public function updateTrangThai(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:cho_xac_nhan,dang_giao,hoan_thanh,huy',
        ]);

        $user = Auth::user();
        $donHang = DonHang::where('doanh_nghiep_id', $user->id)->findOrFail($id);

        $donHang->update(['trang_thai' => $request->trang_thai]);

        // ✅ Nếu chọn "Hoàn thành" → cập nhật thống kê
        if ($request->trang_thai === 'hoan_thanh') {
            $this->capNhatThongKe($donHang);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
    protected function capNhatThongKe($donHang)
    {
        $doanhNghiepId = $donHang->doanh_nghiep_id;
        $ngayHomNay = now()->toDateString();
        $soSanPhamBan = $donHang->chiTietDonHang()->sum('so_luong');

        // 🔎 Kiểm tra xem hôm nay đã có dòng thống kê chưa
        $thongKe = \App\Models\ThongKeBanHang::where('doanh_nghiep_id', $doanhNghiepId)
            ->where('thoi_gian', $ngayHomNay)
            ->first();

        if ($thongKe) {
            // ✅ Nếu đã có → cộng dồn
            $thongKe->tong_doanh_thu += $donHang->tong_tien;
            $thongKe->so_don_hang += 1;
            $thongKe->so_san_pham_ban += $soSanPhamBan;
            $thongKe->save();
        } else {
            // ✅ Nếu chưa có → tạo mới
            \App\Models\ThongKeBanHang::create([
                'doanh_nghiep_id' => $doanhNghiepId,
                'tong_doanh_thu' => $donHang->tong_tien,
                'so_don_hang' => 1,
                'so_san_pham_ban' => $soSanPhamBan,
                'thoi_gian' => $ngayHomNay,
            ]);
        }
    }
}
