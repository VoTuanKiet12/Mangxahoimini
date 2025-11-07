<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use App\Models\DoanhNghiep;
use Carbon\Carbon;

class ThongKeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ✅ Kiểm tra quyền
        if ($user->role !== 'doanh_nghiep') {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        // ✅ Lấy doanh nghiệp tương ứng với user
        $doanhNghiep = DoanhNghiep::where('user_id', $user->id)->first();
        if (!$doanhNghiep) {
            return redirect()->back()->with('error', 'Không tìm thấy doanh nghiệp của bạn.');
        }

        // 📊 Thống kê theo ngày
        $thongKes = DB::table('thong_ke_ban_hang')
            ->where('doanh_nghiep_id', $doanhNghiep->id)
            ->whereYear('thoi_gian', Carbon::now()->year)
            ->orderBy('thoi_gian', 'asc')
            ->get();

        $ngay = $thongKes->pluck('thoi_gian')->map(fn($d) => date('d/m', strtotime($d)));
        $doanhThu = $thongKes->pluck('tong_doanh_thu');
        $soDonHang = $thongKes->pluck('so_don_hang');

        // 📅 Thống kê theo tháng (năm hiện tại)
        $thongKeThang = DB::table('thong_ke_ban_hang')
            ->selectRaw('MONTH(thoi_gian) as thang, SUM(tong_doanh_thu) as tong_doanh_thu, SUM(so_don_hang) as tong_don_hang')
            ->where('doanh_nghiep_id', $doanhNghiep->id)
            ->whereYear('thoi_gian', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(thoi_gian)'))
            ->orderBy(DB::raw('MONTH(thoi_gian)'))
            ->get();

        $thang = $thongKeThang->pluck('thang')->map(fn($m) => 'Tháng ' . $m);
        $doanhThuThang = $thongKeThang->pluck('tong_doanh_thu');
        $soDonHangThang = $thongKeThang->pluck('tong_don_hang');

        // 📈 Trả dữ liệu sang view
        return view('doanhnghiep.thongke.bieudo', compact(
            'ngay',
            'doanhThu',
            'soDonHang',
            'thang',
            'doanhThuThang',
            'soDonHangThang'
        ));
    }

    public function topBanChay()
    {
        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())->first();

        if (!$doanhNghiep) {
            return redirect()->back()->with('error', 'Bạn chưa có doanh nghiệp!');
        }

        $topSanPham = SanPham::select(
            'san_pham.id',
            'san_pham.ten_san_pham',
            'san_pham.hinh_anh',
            DB::raw('COALESCE(SUM(chi_tiet_don_hang.so_luong), 0) as tong_ban')
        )
            ->leftJoin('chi_tiet_don_hang', 'chi_tiet_don_hang.san_pham_id', '=', 'san_pham.id')
            ->where('san_pham.doanh_nghiep_id', $doanhNghiep->id)
            ->groupBy('san_pham.id', 'san_pham.ten_san_pham', 'san_pham.hinh_anh')
            ->orderByDesc('tong_ban')
            ->limit(10)
            ->get();
        return view('doanhnghiep.sanpham.topban', compact('topSanPham'));
    }
}
