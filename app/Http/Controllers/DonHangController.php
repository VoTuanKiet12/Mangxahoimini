<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\ThanhToan;
use App\Models\ThongBao;
use App\Models\DoanhNghiep;
use App\Models\GioHang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonHangController extends Controller
{
    // ⚡ Khi click "Mua ngay" → hiển thị form đặt hàng
    public function showForm(Request $request, $id)
    {
        $sanPham = SanPham::findOrFail($id);

        // ✅ Lấy số lượng từ query ?so_luong=...
        $soLuong = max(1, (int) $request->get('so_luong', 1));

        return view('donhang.thongtin', compact('sanPham', 'soLuong'));
    }

    // ✅ Khi bấm "Xác nhận đặt hàng" → lưu vào CSDL
    public function store(Request $request)
    {
        $request->validate([
            'ten_nguoi_nhan'   => 'required|string|max:255',
            'so_dien_thoai'    => 'required|string|max:20',
            'email_nguoi_nhan' => 'required|email|max:255',
            'dia_chi_giao'     => 'required|string|max:255',
            'phuong_thuc'      => 'required|string',
            'san_pham_id'      => 'required|exists:san_pham,id',
        ]);

        $user = Auth::user();
        $sanPham = SanPham::findOrFail($request->san_pham_id);

        if ($sanPham->so_luong < 1) {
            return back()->with('error', '⚠️ Sản phẩm đã hết hàng.');
        }

        // 🔹 Lấy khuyến mãi hiện tại (nếu có)
        $km = $sanPham->khuyenMaiHienTai()->first();
        $giaSauKhuyenMai = $km ? $sanPham->gia_sau_khuyen_mai : $sanPham->gia;

        // 🔹 Tính toán tổng tiền, VAT
        $soLuong = max(1, (int) $request->so_luong);

        $tongGoc = $sanPham->gia * $soLuong;
        $tongSauGiam = $giaSauKhuyenMai * $soLuong;
        $tongGiam = $tongGoc - $tongSauGiam;
        $vat = $tongSauGiam * 0.1;
        $tongCuoi = $tongSauGiam + $vat;

        DB::beginTransaction();
        try {
            $doanhNghiepId = $sanPham->doanh_nghiep_id ?? null;

            // 🧾 Tạo đơn hàng (lưu tổng tiền cuối cùng sau VAT)
            $donHang = DonHang::create([
                'user_id'          => $user->id,
                'doanh_nghiep_id'  => $doanhNghiepId,
                'ten_nguoi_nhan'   => $request->ten_nguoi_nhan,
                'so_dien_thoai'    => $request->so_dien_thoai,
                'email_nguoi_nhan' => $request->email_nguoi_nhan,
                'dia_chi_giao'     => $request->dia_chi_giao,
                'tong_tien'        => $tongCuoi,
                'trang_thai'       => 'cho_xac_nhan',
            ]);

            // 💰 Chi tiết đơn hàng
            ChiTietDonHang::create([
                'don_hang_id' => $donHang->id,
                'san_pham_id' => $sanPham->id,
                'so_luong'    => $soLuong,
                'don_gia'     => $giaSauKhuyenMai,
            ]);

            // 💵 Thanh toán
            ThanhToan::create([
                'don_hang_id' => $donHang->id,
                'so_tien'     => $tongCuoi,
                'phuong_thuc' => $request->phuong_thuc,
                'trang_thai'  => 'cho_thanh_toan',
            ]);

            // 🔻 Giảm tồn kho sản phẩm
            $sanPham->decrement('so_luong', $soLuong);
            $sanPham->refresh(); // Lấy lại dữ liệu mới nhất từ DB sau khi trừ
            $sanPham->capNhatTrangThaiTheoSoLuong();
            // 🔔 Gửi thông báo cho doanh nghiệp
            if ($doanhNghiepId) {
                $doanhNghiep = DoanhNghiep::find($doanhNghiepId);
                if ($doanhNghiep && $doanhNghiep->user_id) {
                    ThongBao::create([
                        'user_id'  => $doanhNghiep->user_id,
                        'noi_dung' => 'Bạn có đơn hàng mới từ khách hàng "' . $user->name . '" — mã đơn #' . $donHang->id,
                        'link'     => null,
                    ]);
                }
            }

            // 🔔 Gửi thông báo cho người dùng
            ThongBao::create([
                'user_id'  => $user->id,
                'noi_dung' => 'Đặt hàng thành công! Mã đơn #' . $donHang->id . ' — vui lòng chờ doanh nghiệp xác nhận.',
                'link'     => route('donhang.daMua'),
            ]);

            DB::commit();

            return redirect()->route('donhang.daMua')
                ->with('success', 'Đặt hàng thành công! Giá đã bao gồm khuyến mãi và VAT.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    // 📦 Danh sách đơn hàng người dùng đã mua
    public function daMua()
    {
        $user = Auth::user();

        $donHangs = DonHang::where('user_id', $user->id)
            ->with(['chiTietDonHang.sanPham', 'thanhToan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('donhang.damua', compact('donHangs'));
    }
    public function destroy($id)
    {
        $donHang = DonHang::with('doanhNghiep')->findOrFail($id);

        // Kiểm tra quyền xóa
        if ($donHang->user_id !== Auth::id() || !in_array($donHang->trang_thai, ['cho_xac_nhan', 'da_huy'])) {
            return redirect()->back()->with('error', 'Bạn không thể xóa đơn hàng này.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Lưu lại thông tin doanh nghiệp (nếu có)
            $doanhNghiep = $donHang->doanhNghiep;

            // Xóa chi tiết đơn hàng
            $donHang->chiTietDonHang()->delete();

            // Xóa đơn hàng
            $donHang->delete();

            // 🔔 Gửi thông báo cho doanh nghiệp
            if ($doanhNghiep && $doanhNghiep->user_id) {
                ThongBao::create([
                    'user_id'  => $doanhNghiep->user_id,
                    'noi_dung' => 'Khách hàng "' . $user->name . '" đã xóa đơn hàng #' . $donHang->id,
                    'link'     => null,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Đã xóa đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }
    // 🛒 Mua toàn bộ sản phẩm trong giỏ hàng
    public function datHangTuGioHang(Request $request)
    {
        $user = Auth::user();
        $gioHangs = GioHang::where('user_id', $user->id)->with('sanPham')->get();

        if ($gioHangs->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();
        try {
            // Nhóm sản phẩm theo doanh nghiệp
            $nhomTheoDoanhNghiep = $gioHangs->groupBy(fn($g) => $g->sanPham->doanh_nghiep_id);
            $tatCaMaDon = [];

            foreach ($nhomTheoDoanhNghiep as $doanhNghiepId => $items) {
                // 🔹 Tổng gốc (chưa giảm)
                $tongGoc = $items->sum(fn($i) => $i->so_luong * $i->sanPham->gia);

                // 🔹 Tổng sau khi áp dụng khuyến mãi (nếu có)
                $tongSauGiam = $items->sum(function ($i) {
                    $km = $i->sanPham->khuyenMaiHienTai()->first();
                    $gia = $km ? $i->sanPham->gia_sau_khuyen_mai : $i->sanPham->gia;
                    return $i->so_luong * $gia;
                });

                // 🔹 Mức giảm giá và VAT
                $tongGiam = $tongGoc - $tongSauGiam;
                $vat = $tongSauGiam * 0.1;

                // 🔹 Tổng cuối cùng
                $tongCuoi = $tongSauGiam + $vat;

                // 🧾 Tạo đơn hàng
                $donHang = DonHang::create([
                    'user_id'         => $user->id,
                    'doanh_nghiep_id' => $doanhNghiepId,
                    'ten_nguoi_nhan'  => $user->name,
                    'so_dien_thoai'   => $user->so_dien_thoai ?? 'Chưa có',
                    'email_nguoi_nhan' => $user->email,
                    'dia_chi_giao'    => $request->dia_chi_giao ?? 'Chưa cập nhật',
                    'tong_tien'       => $tongCuoi, // ✅ Tổng sau giảm + VAT
                    'trang_thai'      => 'cho_xac_nhan',
                ]);

                $tatCaMaDon[] = $donHang->id;

                // 🔹 Lưu chi tiết đơn hàng
                foreach ($items as $item) {
                    $km = $item->sanPham->khuyenMaiHienTai()->first();
                    $gia = $km ? $item->sanPham->gia_sau_khuyen_mai : $item->sanPham->gia;

                    ChiTietDonHang::create([
                        'don_hang_id' => $donHang->id,
                        'san_pham_id' => $item->san_pham_id,
                        'so_luong'    => $item->so_luong,
                        'don_gia'     => $gia,
                    ]);

                    // Giảm số lượng tồn
                    $item->sanPham->decrement('so_luong', $item->so_luong);
                    $item->sanPham->refresh(); // lấy lại dữ liệu mới từ DB
                    $item->sanPham->capNhatTrangThaiTheoSoLuong();
                }

                // 💵 Tạo bản ghi thanh toán (bao gồm VAT)
                ThanhToan::create([
                    'don_hang_id' => $donHang->id,
                    'so_tien'     => $tongCuoi,
                    'phuong_thuc' => $request->phuong_thuc ?? 'tien_mat',
                    'trang_thai'  => 'cho_thanh_toan',
                ]);

                // 🔔 Thông báo cho doanh nghiệp
                if ($doanhNghiepId) {
                    $doanhNghiep = DoanhNghiep::find($doanhNghiepId);
                    if ($doanhNghiep) {
                        ThongBao::create([
                            'user_id' => $doanhNghiep->user_id,
                            'noi_dung' => 'Bạn có đơn hàng mới từ khách hàng ' . $user->name,
                            'link' => null,
                        ]);
                    }
                }
            }

            // 🔔 Thông báo cho khách hàng
            ThongBao::create([
                'user_id'  => $user->id,
                'noi_dung' => '🛍 Đặt hàng thành công! Các mã đơn: #' . implode(', #', $tatCaMaDon) .
                    ' — vui lòng chờ doanh nghiệp xác nhận.',
                'link'     => route('donhang.daMua'),
            ]);

            // Xóa giỏ hàng
            GioHang::where('user_id', $user->id)->delete();

            DB::commit();
            return redirect()->route('donhang.daMua')->with('success', 'Đặt hàng tất cả sản phẩm thành công! Đã bao gồm khuyến mãi và VAT.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }
    public function hienThiFormThanhToan()
    {
        $user = Auth::user();
        $gioHang = GioHang::where('user_id', $user->id)->with('sanPham')->get();

        if ($gioHang->isEmpty()) {
            return redirect()->route('giohang.index')->with('error', 'Giỏ hàng trống!');
        }

        // Tính toán tổng
        $tongGoc = $gioHang->sum(fn($i) => $i->so_luong * $i->sanPham->gia);
        $tongSauGiam = $gioHang->sum(function ($i) {
            $km = $i->sanPham->khuyenMaiHienTai()->first();
            $gia = $km ? $i->sanPham->gia_sau_khuyen_mai : $i->sanPham->gia;
            return $i->so_luong * $gia;
        });
        $tongGiam = $tongGoc - $tongSauGiam;
        $vat = $tongSauGiam * 0.1;
        $tongCuoi = $tongSauGiam + $vat;

        return view('donhang.muahang', compact('gioHang', 'tongGoc', 'tongGiam', 'vat', 'tongCuoi'));
    }
}
