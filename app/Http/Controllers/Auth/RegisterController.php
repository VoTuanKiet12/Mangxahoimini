<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.auth'); // dùng chung view cho login + register
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
            'so_dien_thoai'         => ['nullable', 'string', 'max:20'],
            'ngay_sinh'             => ['nullable', 'date'],
            'anh_dai_dien'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        // Xử lý upload ảnh đại diện (nếu có)
        $avatarPath = null;
        if ($request->hasFile('anh_dai_dien')) {
            $avatarPath = $request->file('anh_dai_dien')->store('avatars', 'public');
            // => lưu vào storage/app/public/avatars
        }

        $user = User::create([
            'name'          => $data['name'],
            'username'      => $data['username'],
            'email'         => $data['email'],
            'role'          => 'user', // mặc định user
            'password'      => Hash::make($data['password']),
            'so_dien_thoai' => $data['so_dien_thoai'] ?? null,
            'ngay_sinh'     => $data['ngay_sinh'] ?? null,
            'anh_dai_dien'  => $avatarPath, // đường dẫn ảnh
        ]);

        Auth::login($user);

        // ✅ chuyển về trang chủ hoặc trang success
        return view('auth.success');
    }
}
