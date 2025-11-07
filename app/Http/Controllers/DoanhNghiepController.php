<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoanhNghiep;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ThongBao;
use Illuminate\Support\Facades\DB;
use App\Models\BaiViet;

class DoanhNghiepController extends Controller
{
    // Hiển thị form đăng ký doanh nghiệp
    public function create()
    {
        return view('doanhnghiep.create');
    }

    // Lưu thông tin doanh nghiệp
    public function store(Request $request)
    {
        $request->validate([
            'ten_cua_hang' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png',
            'dia_chi' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $doanhNghiep = DoanhNghiep::create([
            'user_id' => Auth::id(),
            'ten_cua_hang' => $request->ten_cua_hang,
            'mo_ta' => $request->mo_ta,
            'logo' => $logoPath,
            'dia_chi' => $request->dia_chi,
            'so_dien_thoai' => $request->so_dien_thoai,
            'trang_thai' => 'cho_duyet',
        ]);

        // cập nhật quyền người dùng thành doanh nghiệp
        $user = Auth::user();
        $user->role = 'doanh_nghiep';
        $user->save();

        return redirect()->route('trangchu')->with('success', 'Đăng ký doanh nghiệp thành công! Hãy chờ admin duyệt.');
    }

    // ===============================
    // QUẢN LÝ DOANH NGHIỆP (ADMIN)
    // ===============================

    // Danh sách doanh nghiệp chờ duyệt
    public function index()
    {
        $choDuyet = DoanhNghiep::where('trang_thai', 'cho_duyet')->get();
        $hoatDong = DoanhNghiep::where('trang_thai', 'hoat_dong')->get();
        $biTuChoi = DoanhNghiep::where('trang_thai', 'tu_choi')->get();
        $tongUser = User::count();
        $tongBaiViet = BaiViet::count();
        $tongDoanhNghiep = DoanhNghiep::count();

        // ⚙️ View mới
        return view('admin.quanlydoanhnghiep', compact('choDuyet', 'hoatDong', 'biTuChoi', 'tongUser', 'tongBaiViet', 'tongDoanhNghiep'));
    }


    // Duyệt doanh nghiệp
    public function approve($id)
    {
        $doanhNghiep = DoanhNghiep::findOrFail($id);
        $doanhNghiep->update(['trang_thai' => 'hoat_dong']);

        // Cập nhật vai trò người dùng
        $user = $doanhNghiep->user;
        $user->role = 'doanh_nghiep';
        $user->save();

        return redirect()->back()->with('success', 'Doanh nghiệp đã được duyệt!');
    }

    // Từ chối doanh nghiệp

    public function reject($id)
    {
        $doanhNghiep = DoanhNghiep::findOrFail($id);
        $user = $doanhNghiep->user;

        DB::beginTransaction();

        try {
            if ($user) {
                ThongBao::create([
                    'user_id' => $user->id,
                    'noi_dung' => 'Đăng ký doanh nghiệp "' . $doanhNghiep->ten_cua_hang . '" của bạn đã bị từ chối và đã bị xóa khỏi hệ thống.',
                    'link' => null,
                ]);

                // 🔄 Trả lại quyền người dùng thường
                $user->update(['role' => 'user']);
            }
            $doanhNghiep->delete();
            DB::commit();
            return redirect()->back()->with('error', 'Đã từ chối và xóa doanh nghiệp khỏi hệ thống.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi từ chối doanh nghiệp: ' . $e->getMessage());
        }
    }
    public function quanly()
    {
        return view('doanhnghiep.quanly');
    }
    public function thongKe()
    {
        $user = auth()->user();

        // Nếu user chưa có doanh nghiệp → tạo mặc định
        if (!$user->doanh_nghiep) {
            $doanhNghiep = \App\Models\DoanhNghiep::create([
                'user_id' => $user->id,
                'ten_cua_hang' => 'Doanh nghiệp của ' . $user->name,
                'dia_chi' => 'Chưa cập nhật',
                'so_dien_thoai' => 'Chưa có',
            ]);
        } else {
            $doanhNghiep = $user->doanh_nghiep;
        }

        $doanhNghiepId = $doanhNghiep->id;

        $tongDoanhThu = DB::table('don_hang')
            ->where('doanh_nghiep_id', $doanhNghiepId)
            ->where('trang_thai', 'hoan_thanh')
            ->sum('tong_tien');

        $soDonHang = DB::table('don_hang')
            ->where('doanh_nghiep_id', $doanhNghiepId)
            ->count();

        $soSanPhamBan = DB::table('chi_tiet_don_hang')
            ->join('don_hang', 'chi_tiet_don_hang.don_hang_id', '=', 'don_hang.id')
            ->where('don_hang.doanh_nghiep_id', $doanhNghiepId)
            ->sum('so_luong');

        return view('doanhnghiep.thongke', compact('tongDoanhThu', 'soDonHang', 'soSanPhamBan'));
    }
    public function showThongTin()
    {
        $user = Auth::user();
        $doanhNghiep = $user->doanh_nghiep;

        if (!$doanhNghiep) {
            return redirect()->route('doanhnghiep.create')
                ->with('error', ' Bạn chưa đăng ký doanh nghiệp.');
        }

        return view('doanhnghiep.thongtin', compact('doanhNghiep'));
    }
    public function edit($id)
    {
        $doanhNghiep = \App\Models\DoanhNghiep::findOrFail($id);

        // Chỉ cho phép doanh nghiệp của chính user chỉnh sửa
        if ($doanhNghiep->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa doanh nghiệp này.');
        }

        return view('doanhnghiep.edit', compact('doanhNghiep'));
    }

    public function update(Request $request, $id)
    {
        $doanhNghiep = \App\Models\DoanhNghiep::findOrFail($id);

        if ($doanhNghiep->user_id !== Auth::id()) {
            abort(403, 'Không được phép cập nhật doanh nghiệp này.');
        }

        $request->validate([
            'ten_cua_hang' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'dia_chi' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
        ]);

        $data = $request->only(['ten_cua_hang', 'mo_ta', 'dia_chi', 'so_dien_thoai']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $doanhNghiep->update($data);

        return redirect()->route('doanhnghiep.thongtin')
            ->with('success', ' Cập nhật thông tin doanh nghiệp thành công!');
    }
}
