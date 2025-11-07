<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    // ------------------------------
    // 👉 Trang cá nhân
    // ------------------------------
    public function showProfile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
        // view resources/views/user/profile.blade.php
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập trước!');
        }

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'dia_chi'       => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'ngay_sinh'     => 'nullable|date',
        ]);

        $user->update($data);

        return redirect()->route('user.profile')->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    // ------------------------------
    // 👉 Ảnh đại diện (giữ nguyên code cũ)
    // ------------------------------
    public function showAvatarForm()
    {
        $user = Auth::user();
        return view('user.avatar', compact('user')); // view resources/views/user/avatar.blade.php
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'anh_dai_dien' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập trước!');
        }

        if ($request->hasFile('anh_dai_dien')) {
            // Lưu file mới vào thư mục storage/app/public/avatars
            $path = $request->file('anh_dai_dien')->store('avatars', 'public');

            // Xóa ảnh cũ nếu tồn tại
            if ($user->anh_dai_dien && Storage::disk('public')->exists($user->anh_dai_dien)) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }

            // Cập nhật đường dẫn trong DB
            $user->anh_dai_dien = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Ảnh đại diện đã được cập nhật!');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $authId = Auth::id();

        // Lấy bài viết của user này
        $baiviets = $user->baiviets()->latest('ngay_dang')->get();

        // Nếu là chính mình → không cần kiểm tra bạn bè
        if ($authId === $user->id) {
            $friendStatus = null;
        } else {
            // Kiểm tra trạng thái kết bạn
            $relation = DB::table('ket_ban')
                ->where(function ($q) use ($authId, $user) {
                    $q->where('user_id', $authId)
                        ->where('ban_be_id', $user->id);
                })
                ->orWhere(function ($q) use ($authId, $user) {
                    $q->where('user_id', $user->id)
                        ->where('ban_be_id', $authId);
                })
                ->first();

            $friendStatus = $relation->trang_thai ?? null;
        }

        return view('user.show', compact('user', 'baiviets', 'friendStatus'));
    }
    public function updateCover(Request $request)
    {
        $request->validate([
            'anh_bia' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập trước!');
        }

        if ($request->hasFile('anh_bia')) {
            $path = $request->file('anh_bia')->store('anhbia', 'public');

            // Xóa ảnh cũ nếu có
            if ($user->anh_bia && Storage::disk('public')->exists($user->anh_bia)) {
                Storage::disk('public')->delete($user->anh_bia);
            }

            $user->anh_bia = $path;
            $user->save();
        }

        return back()->with('success', 'Ảnh bìa đã được cập nhật!');
    }
}
