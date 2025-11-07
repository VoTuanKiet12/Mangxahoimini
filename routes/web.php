<?php

use Illuminate\Support\Facades\Route;

// =======================
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TrangChuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\TimkiemController;
use App\Http\Controllers\BaiVietController;
use App\Http\Controllers\BinhLuanController;
use App\Http\Controllers\LuotThichController;
use App\Http\Controllers\KetBanController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\TinNhanController;
use App\Http\Controllers\MauTinNhanController;
use App\Http\Controllers\NhomController;
use App\Http\Controllers\DoanhNghiepController;
use App\Http\Controllers\LoaiSanPhamController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\DanhGiaSanPhamController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\DonHangDoanhNghiepController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\DoanhNghiepBaiVietController;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);



Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/nguoidung', [AdminController::class, 'index'])->name('admin.nguoidung');
    Route::patch('/user/{id}/toggle', [AdminController::class, 'toggleUser'])->name('admin.toggleUser');
    Route::patch('/admin/user/{id}/role', [AdminController::class, 'updateRole'])->name('admin.updateRole');

    Route::get('/doanh-nghiep', [DoanhNghiepController::class, 'index'])->name('admin.doanhnghiep.index');
    Route::patch('/doanh-nghiep/{id}/duyet', [DoanhNghiepController::class, 'approve'])->name('admin.doanhnghiep.duyet');
    Route::patch('/doanh-nghiep/{id}/tu-choi', [DoanhNghiepController::class, 'reject'])->name('admin.doanhnghiep.tuchoi');
    Route::get('/admin/doanhnghiep', [AdminController::class, 'nguoiDungDoanhNghiep'])
        ->name('admin.doanhnghiep.list');
    Route::get('/loai-san-pham', [LoaiSanPhamController::class, 'index'])->name('admin.loaisp.danhsach');
    Route::post('/loai-san-pham/them', [LoaiSanPhamController::class, 'store'])->name('admin.loaisp.them');
    Route::patch('/loai-san-pham/sua/{id}', [LoaiSanPhamController::class, 'update'])->name('admin.loaisp.sua');
    Route::delete('/loai-san-pham/xoa/{id}', [LoaiSanPhamController::class, 'destroy'])->name('admin.loaisp.xoa');

    Route::get('/baiviet', [BaiVietController::class, 'index'])->name('admin.baiviet');
    Route::delete('/baiviet/{id}', [BaiVietController::class, 'destroy'])->name('admin.baiviet.destroy');
});



Route::middleware('auth')->group(function () {

    Route::get('/trangchu', [TrangChuController::class, 'index'])->name('trangchu');

    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/profile', [UserController::class, 'showProfile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/user/avatar', [UserController::class, 'showAvatarForm'])->name('user.avatar.form');
    Route::post('/user/avatar', [UserController::class, 'updateAvatar'])->name('user.avatar');
    Route::post('/user/update-avatar', [UserController::class, 'updateAvatar'])->name('user.update.avatar');
    Route::post('/user/update-cover', [UserController::class, 'updateCover'])->name('user.update.cover');

    Route::post('/baiviet/store', [BaiVietController::class, 'store'])->name('baiviet.store');
    Route::get('/baiviet/{id}', [BaiVietController::class, 'show'])->name('baiviet.show');
    Route::delete('/baiviet/{id}', [BaiVietController::class, 'destroy'])->name('baiviet.destroy');
    Route::get('/bai-viet/{id}', [BaiVietController::class, 'show'])->name('baiviet.chitiet');
    Route::get('/video', [BaiVietController::class, 'tatCaVideo'])->name('bai-viet.video');
    Route::get('/doanhnghiep/baiviet/tao', [App\Http\Controllers\DoanhNghiepBaiVietController::class, 'create'])
        ->name('doanhnghiep.baiviet.create');

    Route::post('/doanhnghiep/baiviet/tao', [App\Http\Controllers\DoanhNghiepBaiVietController::class, 'store'])
        ->name('doanhnghiep.baiviet.store');
    Route::post('/binhluan', [BinhLuanController::class, 'store'])->name('binhluan.store');
    Route::get('/binhluan/{id}', [BinhLuanController::class, 'index'])->name('binhluan.index');
    Route::delete('/binhluan/{id}', [BinhLuanController::class, 'destroy'])->name('binhluan.destroy');
    Route::post('/baiviet/{id}/like', [LuotThichController::class, 'store'])->name('like');
    Route::post('/baiviet/{id}/unlike', [LuotThichController::class, 'destroy'])->name('unlike');

    Route::get('/ban-be', [KetBanController::class, 'tatCaBanBe'])->name('ketban.ban_be');
    Route::get('/loi-moi-ket-ban', [KetBanController::class, 'tatCaLoiMoi'])->name('ketban.loimoi');
    Route::get('/goi-y-ban-be', [KetBanController::class, 'goiYBanBe'])->name('ketban.goi_y');
    Route::post('/ket-ban/send/{id}', [KetBanController::class, 'send'])->name('ketban.send');
    Route::post('/ket-ban/accept/{id}', [KetBanController::class, 'accept'])->name('ketban.accept');
    Route::post('/ket-ban/decline/{id}', [KetBanController::class, 'decline'])->name('ketban.decline');
    Route::post('/ketban/cancel/{id}', [KetBanController::class, 'cancel'])->name('ketban.cancel');

    Route::post('/story', [StoryController::class, 'store'])->name('story.store');
    Route::get('/story/clean', [StoryController::class, 'cleanExpired'])->name('story.clean');
    Route::delete('/story/{id}', [StoryController::class, 'destroy'])->name('story.destroy');

    Route::get('/tin-nhan/{id}', [TinNhanController::class, 'show']);
    Route::post('/tin-nhan/gui', [TinNhanController::class, 'send']);
    Route::get('/kiemtra-tinnhan-moi', [TinNhanController::class, 'kiemTraMoi']);
    Route::get('/danhdau-dadoc/{friend_id}', [TinNhanController::class, 'danhDauDaDoc']);
    Route::delete('/tin-nhan/xoa/{id}', [TinNhanController::class, 'xoaTinNhan'])->name('tin-nhan.xoa');

    Route::get('/lay-mau-chat/{friendId}', [MauTinNhanController::class, 'layMau']);
    Route::post('/luu-mau-chat', [MauTinNhanController::class, 'luuMau']);
    Route::post('/luu-anh-nen-chat', [MauTinNhanController::class, 'luuAnhNen']);
    Route::post('/xoa-anh-nen-chat', [MauTinNhanController::class, 'xoaAnhNen']);

    Route::prefix('nhom')->group(function () {
        Route::get('/tao', [NhomController::class, 'create'])->name('nhom.create');
        Route::post('/tao', [NhomController::class, 'store'])->name('nhom.store');
        Route::get('/danhsach', [NhomController::class, 'index'])->name('nhom.index');
        Route::get('/{id}', [NhomController::class, 'show'])->name('nhom.show');
        Route::get('/{id}/edit', [NhomController::class, 'edit'])->name('nhom.edit');
        Route::put('/{id}', [NhomController::class, 'update'])->name('nhom.update');
        Route::delete('/{id}/xoa', [NhomController::class, 'destroy'])->name('nhom.destroy');
        Route::delete('/{id}/roi-nhom', [NhomController::class, 'leave'])->name('nhom.leave');

        Route::get('/{id}/quanlynhom', [NhomController::class, 'quanlynhom'])->name('nhom.quanlynhom');
        Route::post('/{nhomId}/update-role/{userId}', [NhomController::class, 'updateMemberRole'])->name('nhom.updateRole');
        Route::delete('/{nhom}/kick/{user}', [NhomController::class, 'kickMember'])->name('nhom.kick');

        Route::post('/{id}/moi-ban', [NhomController::class, 'inviteFriend'])->name('nhom.invite');
        Route::post('/{id}/chap-nhan', [NhomController::class, 'acceptInvite'])->name('nhom.accept');
        Route::post('/{id}/tu-choi', [NhomController::class, 'rejectInvite'])->name('nhom.reject');
        Route::get('/{id}/danh-sach-moi', [NhomController::class, 'getAvailableFriends']);

        Route::get('/{id}/tin-nhan', [NhomController::class, 'messages'])->name('nhom.messages');
        Route::get('/{id}/messages', [NhomController::class, 'getMessages'])->name('nhom.getMessages');
        Route::post('/{id}/send-message', [NhomController::class, 'sendMessage'])->name('nhom.sendMessage');
        Route::delete('/tin-nhan/xoa/{id}', [NhomController::class, 'deleteGroupMessage'])->name('nhom.deleteGroupMessage');
    });

    Route::get('/voicechat', fn() => view('voicechat.index'));

    Route::get('/dang-ky-doanh-nghiep', [DoanhNghiepController::class, 'create'])->name('doanhnghiep.create');
    Route::post('/dang-ky-doanh-nghiep', [DoanhNghiepController::class, 'store'])->name('doanhnghiep.store');

    Route::get('/doanhnghiep/quan-ly', fn() => redirect()->route('doanhnghiep.thongtin'))->name('doanhnghiep.quanly');
    Route::get('/doanhnghiep/thongtin', [DoanhNghiepController::class, 'showThongTin'])->name('doanhnghiep.thongtin');
    Route::get('/doanhnghiep/{id}/edit', [DoanhNghiepController::class, 'edit'])->name('doanhnghiep.edit');
    Route::put('/doanhnghiep/{id}', [DoanhNghiepController::class, 'update'])->name('doanhnghiep.update');

    Route::get('/doanhnghiep/thongke', [ThongKeController::class, 'index'])->name('doanhnghiep.thongke');
    Route::get('/doanhnghiep/sanpham/top-ban-chay', [ThongKeController::class, 'topBanChay'])->name('doanhnghiep.sanpham.top_ban_chay');

    Route::prefix('doanhnghiep')->group(function () {
        Route::get('/dang-san-pham', [SanPhamController::class, 'create'])->name('doanhnghiep.dangsanpham');
        Route::post('/dang-san-pham', [SanPhamController::class, 'store'])->name('sanpham.store');

        Route::get('/quanly-sanpham', [SanPhamController::class, 'indexQuanLy'])->name('doanhnghiep.sanpham.index');
        Route::get('/sanpham/edit/{id}', [SanPhamController::class, 'edit'])->name('doanhnghiep.sanpham.edit');
        Route::put('/sanpham/update/{id}', [SanPhamController::class, 'update'])->name('doanhnghiep.sanpham.update');
        Route::delete('/sanpham/delete/{id}', [SanPhamController::class, 'destroy'])->name('doanhnghiep.sanpham.delete');
        Route::get('/sanpham/{id}/danhgia', [SanPhamController::class, 'xemDanhGia'])->name('doanhnghiep.sanpham.danhgia');
        Route::get('/sanpham/xuat', [SanPhamController::class, 'getXuat'])->name('doanhnghiep.sanpham.xuat');
    });

    Route::get('/don-hang', [DonHangDoanhNghiepController::class, 'index'])->name('doanhnghiep.donhang.index');
    Route::get('/don-hang/{id}', [DonHangDoanhNghiepController::class, 'show'])->name('doanhnghiep.donhang.show');
    Route::put('/don-hang/{id}', [DonHangDoanhNghiepController::class, 'updateTrangThai'])->name('doanhnghiep.donhang.update');
    Route::get('/donhang/thanhtoan-giohang', [DonHangController::class, 'hienThiFormThanhToan'])
        ->name('donhang.thanhtoanGioHang');
    Route::post('/donhang/dat-gio-hang', [DonHangController::class, 'datHangTuGioHang'])
        ->name('donhang.datGioHang');

    Route::get('/san-pham', [SanPhamController::class, 'index'])->name('sanpham.index');
    Route::get('/sanpham/{id}', [SanPhamController::class, 'show'])->name('sanpham.chitiet');
    Route::post('/sanpham/nhap', [SanPhamController::class, 'postNhap'])->name('sanpham.nhap');

    Route::get('/giohang', [GioHangController::class, 'index'])->name('giohang.index');
    Route::get('/giohang/dem', [GioHangController::class, 'demSoLuong'])->name('giohang.dem');
    Route::post('/giohang/them', [GioHangController::class, 'them'])->name('giohang.them');
    Route::delete('/giohang/xoa/{id}', [GioHangController::class, 'xoa'])->name('giohang.xoa');
    Route::delete('/giohang/xoa-tat-ca', [GioHangController::class, 'xoaTatCa'])->name('giohang.xoaTatCa');
    Route::patch('/giohang/{id}/tang', [GioHangController::class, 'tang'])->name('giohang.tang');
    Route::patch('/giohang/{id}/giam', [GioHangController::class, 'giam'])->name('giohang.giam');
    Route::post('/dat-hang-gio-hang', [DonHangController::class, 'datHangTuGioHang'])->name('donhang.datGioHang');


    Route::get('/donhang/muangay/{id}', [DonHangController::class, 'showForm'])->name('donhang.muangay');

    Route::post('/donhang/luu', [DonHangController::class, 'store'])->name('donhang.store');
    Route::get('/donhang/da-mua', [DonHangController::class, 'daMua'])->name('donhang.daMua');
    Route::delete('/donhang/{id}/xoa', [DonHangController::class, 'destroy'])->name('donhang.xoa');

    Route::post('/thong-bao/danh-dau-da-doc', [ThongBaoController::class, 'danhDauDaDoc'])->name('thongbao.danhdau');
    Route::post('/danh-gia', [DanhGiaSanPhamController::class, 'store'])->name('danhgia.store');
    Route::delete('/danhgia/{id}', [DanhGiaSanPhamController::class, 'destroy'])->name('danhgia.destroy');
    Route::post('/danhgia/{id}/reply', [DanhGiaSanPhamController::class, 'reply'])
        ->name('danhgia.reply');

    Route::resource('khuyenmai', KhuyenMaiController::class);

    Route::get('/sidebar', [SidebarController::class, 'index'])->name('sidebar');
    Route::get('/tim-kiem', [TimkiemController::class, 'index'])->name('timkiem');
});
