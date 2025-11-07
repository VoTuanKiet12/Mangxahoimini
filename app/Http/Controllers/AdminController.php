<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BaiViet;
use App\Models\BinhLuan;
use App\Models\DoanhNghiep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $tongUser = User::count();
        $tongBaiViet = BaiViet::count();
        $tongDoanhNghiep = DoanhNghiep::count();

        $users = User::paginate(10);

        return view('admin.nguoidung', compact('tongUser', 'tongBaiViet', 'tongDoanhNghiep', 'users'));
    }

    // ✅ Trang quản lý bài viết
    public function baiViet()
    {
        $baiViet = BaiViet::with('user')->latest()->paginate(10);

        $tongBaiViet = BaiViet::count();
        $tongUser = User::count();
        $tongBinhLuan = BinhLuan::count();

        return view('admin.baiviet', compact('baiViet', 'tongBaiViet', 'tongUser', 'tongBinhLuan'));
    }

    // ✅ Trang quản lý doanh nghiệp
    public function doanhNghiep()
    {
        $doanhNghiep = DoanhNghiep::paginate(10);

        $tongDoanhNghiep = DoanhNghiep::count();
        $tongUser = User::count();
        $tongBaiViet = BaiViet::count();

        return view('admin.doanhnghiep.index', compact('doanhNghiep', 'tongDoanhNghiep', 'tongUser', 'tongBaiViet'));
    }
    public function toggleUser($id)
    {
        $user = User::findOrFail($id);

        // Không cho admin tự khóa mình
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Không thể tự vô hiệu hóa tài khoản của chính bạn.');
        }

        $user->trang_thai = $user->trang_thai === 'hoat_dong' ? 'vo_hieu' : 'hoat_dong';
        $user->save();

        return back()->with('success', 'Cập nhật trạng thái tài khoản thành công!');
    }
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Không cho admin tự hạ cấp chính mình
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không thể thay đổi vai trò của chính mình.');
        }

        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật vai trò thành công!');
    }
}
