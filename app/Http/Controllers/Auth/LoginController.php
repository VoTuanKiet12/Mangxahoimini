<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.auth');
    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Tài khoản không tồn tại.',
            ]);
        }

        if ($user->trang_thai === 'vo_hieu') {
            return back()->withErrors([
                'username' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Chào mừng quản trị viên!');
            } else {
                return redirect()->intended(route('trangchu'))->with('success', 'Đăng nhập thành công!');
            }
        }

        return back()->withErrors([
            'username' => 'Sai tài khoản hoặc mật khẩu.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    protected function username()
    {
        return 'username';
    }
}
