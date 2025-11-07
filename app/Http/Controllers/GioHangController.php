<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GioHang;
use App\Models\SanPham;
use Illuminate\Support\Facades\Auth;

class GioHangController extends Controller
{
    /**
     * 🧮 Lấy tổng số lượng sản phẩm trong giỏ hàng của user hiện tại (AJAX)
     */
    public function demSoLuong()
    {
        if (Auth::check()) {
            $soLuong = GioHang::where('user_id', Auth::id())->sum('so_luong');
            return response()->json(['so_luong' => $soLuong]);
        }

        return response()->json(['so_luong' => 0]);
    }

    /**
     * 🗑️ Xóa toàn bộ giỏ hàng
     */
    public function xoaTatCa()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
        }

        GioHang::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng.']);
    }

    /**
     * 👁️ Đánh dấu "đã xem giỏ hàng" (nếu bạn có cột trạng thái)
     */
    public function danhDauDaXem()
    {
        return response()->json(['success' => true]);
    }

    /**
     * 🛒 Trang hiển thị giỏ hàng chính
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng.');
        }

        $gioHang = GioHang::where('user_id', Auth::id())
            ->with('sanPham')
            ->get();

        if ($gioHang->isEmpty()) {
            return view('giohang.index', ['gioHang' => collect(), 'tongGoc' => 0, 'tongGiam' => 0, 'tongSauGiam' => 0, 'vat' => 0, 'tongCuoi' => 0]);
        }

        // Tổng gốc
        $tongGoc = $gioHang->sum(fn($gh) => $gh->so_luong * $gh->sanPham->gia);

        // Tổng sau giảm
        $tongSauGiam = $gioHang->sum(function ($gh) {
            $km = $gh->sanPham->khuyenMaiHienTai()->first();
            $gia = $km ? $gh->sanPham->gia_sau_khuyen_mai : $gh->sanPham->gia;
            return $gh->so_luong * $gia;
        });

        // Tổng giảm giá
        $tongGiam = $tongGoc - $tongSauGiam;

        // ✅ VAT 10%
        $vat = $tongSauGiam * 0.1;

        // ✅ Tổng cuối cùng (sau VAT)
        $tongCuoi = $tongSauGiam + $vat;

        return view('giohang.index', compact('gioHang', 'tongGoc', 'tongGiam', 'tongSauGiam', 'vat', 'tongCuoi'));
    }


    /**
     * ➕ Thêm sản phẩm vào giỏ hàng
     */
    public function them(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.'], 401);
        }

        $sanPhamId = $request->input('san_pham_id');
        $soLuong = (int) $request->input('so_luong', 1);

        $sanPham = SanPham::find($sanPhamId);
        if (!$sanPham) {
            return response()->json(['error' => 'Sản phẩm không tồn tại.'], 404);
        }

        // Nếu số lượng không hợp lệ
        if ($soLuong <= 0) {
            return response()->json(['error' => 'Số lượng phải lớn hơn 0.'], 400);
        }

        // Kiểm tra sản phẩm trong giỏ hàng
        $gioHang = GioHang::where('user_id', Auth::id())
            ->where('san_pham_id', $sanPhamId)
            ->first();

        if ($gioHang) {
            // Đã có → tăng thêm số lượng
            $gioHang->so_luong += $soLuong;
            $gioHang->save();
        } else {
            // Chưa có → tạo mới
            GioHang::create([
                'user_id' => Auth::id(),
                'san_pham_id' => $sanPhamId,
                'so_luong' => $soLuong,
                'ngay_them' => now(),
            ]);
        }

        return response()->json(['success' => 'Đã thêm vào giỏ hàng thành công!']);
    }
    public function xoa($id)
    {
        $item = GioHang::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Đã xoá sản phẩm khỏi giỏ hàng.');
    }
    public function tang($id)
    {
        $item = GioHang::with('sanPham')->findOrFail($id);

        // 🔒 Kiểm tra tồn kho
        if ($item->so_luong >= $item->sanPham->so_luong) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã đạt số lượng tồn kho tối đa.'
            ]);
        }

        // ✅ Cập nhật số lượng
        $item->so_luong++;
        $item->save();

        // ✅ Giá sau giảm của sản phẩm
        $km = $item->sanPham->khuyenMaiHienTai()->first();
        $giaSauGiam = $km ? $item->sanPham->gia_sau_khuyen_mai : $item->sanPham->gia;
        $tong = $giaSauGiam * $item->so_luong;

        // ✅ Tính tổng giỏ hàng
        $gioHang = GioHang::where('user_id', auth()->id())->with('sanPham')->get();
        $tongGoc = $gioHang->sum(fn($gh) => $gh->so_luong * $gh->sanPham->gia);
        $tongSauGiam = $gioHang->sum(function ($gh) {
            $km = $gh->sanPham->khuyenMaiHienTai()->first();
            $gia = $km ? $gh->sanPham->gia_sau_khuyen_mai : $gh->sanPham->gia;
            return $gh->so_luong * $gia;
        });
        $tongGiam = $tongGoc - $tongSauGiam;

        // ✅ Thêm VAT 10%
        $vat = $tongSauGiam * 0.1;
        $tongCuoi = $tongSauGiam + $vat;

        return response()->json([
            'success' => true,
            'so_luong' => $item->so_luong,
            'tong' => number_format($tong, 0, ',', '.') . '₫',
            'tong_goc' => $tongGoc,
            'tong_giam' => $tongGiam,
            'vat' => $vat,
            'tong_tat_ca' => number_format($tongCuoi, 0, ',', '.') . '₫'
        ]);
    }

    public function giam($id)
    {
        $item = GioHang::with('sanPham')->findOrFail($id);

        // ✅ Giới hạn tối thiểu
        if ($item->so_luong <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng tối thiểu là 1 sản phẩm.'
            ]);
        }

        // ✅ Giảm số lượng
        $item->so_luong--;
        $item->save();

        // ✅ Lấy giỏ hàng hiện tại
        $gioHang = GioHang::where('user_id', auth()->id())->with('sanPham')->get();

        if ($gioHang->isEmpty()) {
            return response()->json([
                'deleted' => true,
                'tong_goc' => 0,
                'tong_giam' => 0,
                'vat' => 0,
                'tong_tat_ca' => '0₫'
            ]);
        }

        // ✅ Tính tổng gốc / giảm / sau giảm
        $tongGoc = $gioHang->sum(fn($gh) => $gh->so_luong * $gh->sanPham->gia);
        $tongSauGiam = $gioHang->sum(function ($gh) {
            $km = $gh->sanPham->khuyenMaiHienTai()->first();
            $gia = $km ? $gh->sanPham->gia_sau_khuyen_mai : $gh->sanPham->gia;
            return $gh->so_luong * $gia;
        });
        $tongGiam = $tongGoc - $tongSauGiam;

        // ✅ VAT 10%
        $vat = $tongSauGiam * 0.1;
        $tongCuoi = $tongSauGiam + $vat;

        // ✅ Tổng riêng của sản phẩm hiện tại
        $km = $item->sanPham->khuyenMaiHienTai()->first();
        $giaSauGiam = $km ? $item->sanPham->gia_sau_khuyen_mai : $item->sanPham->gia;
        $tong = $giaSauGiam * $item->so_luong;

        return response()->json([
            'success' => true,
            'so_luong' => $item->so_luong,
            'tong' => number_format($tong, 0, ',', '.') . '₫',
            'tong_goc' => $tongGoc,
            'tong_giam' => $tongGiam,
            'vat' => $vat,
            'tong_tat_ca' => number_format($tongCuoi, 0, ',', '.') . '₫'
        ]);
    }
}
