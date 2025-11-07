<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\BaiViet;
use App\Models\SanPham;
use App\Models\ThongBao;

class DoanhNghiepBaiVietController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        // Kiểm tra user có doanh nghiệp chưa
        $doanhNghiep = $user->doanh_nghiep; // ✅ Đúng tên hàm


        // Lấy sản phẩm thuộc doanh nghiệp
        $sanPhams = $doanhNghiep->sanPham()->get();

        return view('doanhnghiep.baiviet.create', compact('sanPhams'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'noi_dung'   => 'nullable|string',
            'san_pham_id' => 'nullable|exists:san_pham,id',
            'hinh_anh'   => 'nullable|array',
            'hinh_anh.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'video'      => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
        ]);

        $user = Auth::user();
        $doanhNghiep = $user->doanh_nghiep;

        if (!$doanhNghiep) {
            return redirect()->back()->with('error', 'Bạn chưa có doanh nghiệp hoạt động!');
        }

        // ✅ Tạo bài viết mới
        $baiViet = new BaiViet();
        $baiViet->user_id = $user->id;
        $baiViet->noi_dung = $request->noi_dung;
        $baiViet->san_pham_id = $request->san_pham_id;

        // Ảnh
        $images = [];
        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                $images[] = $file->store('baiviet', 'public');
            }
        }
        if (!empty($images)) {
            $baiViet->hinh_anh = $images;
        }

        // Video
        if ($request->hasFile('video')) {
            $baiViet->video = $request->file('video')->store('baiviet', 'public');
        }

        $baiViet->save();

        // Gửi thông báo cho bạn bè hoặc khách hàng (tuỳ bạn mở rộng)
        ThongBao::create([
            'user_id'  => $user->id,
            'noi_dung' => 'Doanh nghiệp ' . $doanhNghiep->ten_cua_hang . ' vừa đăng bài mới!',
            'link'     => route('baiviet.chitiet', $baiViet->id),
        ]);

        return redirect()->route('doanhnghiep.baiviet.create')->with('success', 'Đăng bài thành công!');
    }
}
