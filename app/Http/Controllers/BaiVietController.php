<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaiViet;
use App\Models\KetBan;
use App\Models\User;
use App\Models\BinhLuan;
use App\Models\ThongBao;
use App\Models\DoanhNghiep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BaiVietController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'noi_dung'   => 'nullable|string',

            // kiểm tra mảng ảnh (tối đa 4 ảnh)
            'hinh_anh'   => 'nullable|array',  // không giới hạn số lượng
            'hinh_anh.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            // video tối đa 10MB
            'video'      => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
        ]);

        $baiviet = new BaiViet();
        $baiviet->user_id = Auth::id();
        $baiviet->noi_dung = $request->noi_dung;

        // Xử lý nhiều ảnh
        $images = [];
        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                $images[] = $file->store('baiviet', 'public');
            }
        }
        if (!empty($images)) {
            $baiviet->hinh_anh = $images; // không cần json_encode, Laravel tự làm
        }

        // Video
        if ($request->hasFile('video')) {
            $baiviet->video = $request->file('video')->store('baiviet', 'public');
        }

        $baiviet->save();
        $user = Auth::user();
        $banBes = KetBan::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('ban_be_id', $user->id);
        })
            ->where('trang_thai', 'chap_nhan')
            ->get();

        foreach ($banBes as $banBe) {
            $banBeId = $banBe->user_id == $user->id ? $banBe->ban_be_id : $banBe->user_id;
            ThongBao::create([
                'user_id'  => $banBeId,
                'noi_dung' => '' . $user->name . ' vừa đăng một bài viết mới!',
                'link' => route('baiviet.chitiet', $baiviet->id),
            ]);
        }
        return redirect()->back()->with('success', 'Đăng bài thành công!');
    }

    public function destroy($id)
    {
        $post = BaiViet::findOrFail($id);
        $user = Auth::user();

        // ✅ Nếu là user thường => chỉ xóa bài của chính mình
        if ($user->role !== 'admin' && $post->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bài viết này.');
        }

        // ✅ Xóa tất cả ảnh (nếu có)
        if ($post->hinh_anh) {
            $images = is_array($post->hinh_anh) ? $post->hinh_anh : json_decode($post->hinh_anh, true);
            foreach ($images as $img) {
                if ($img) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        // ✅ Xóa video (nếu có)
        if ($post->video) {
            Storage::disk('public')->delete($post->video);
        }

        // ✅ Xóa bài viết khỏi database
        $post->delete();

        return redirect()->back()->with('success', 'Đã xóa bài viết thành công.');
    }
    public function show($id)
    {
        $baiViet = BaiViet::with([
            'user',
            'binhLuan' => function ($query) {
                $query->orderBy('ngay_binh_luan', 'desc')  // sắp xếp bình luận mới nhất trước
                    ->with('user');
            }
        ])->findOrFail($id);

        return view('baiviet.chitiet', compact('baiViet'));
    }
    public function tatCaVideo()
    {
        // Lấy tất cả bài viết có video, kèm thông tin người đăng
        $dsVideo = BaiViet::with('user')
            ->whereNotNull('video')
            ->orderBy('ngay_dang', 'desc')
            ->get();

        return view('baiviet.ds_video', compact('dsVideo'));
    }
    public function index()
    {
        $baiViet = BaiViet::with('user')->latest()->paginate(5);

        $tongBaiViet = BaiViet::count();
        $tongUser = User::count();
        $tongDoanhNghiep = DoanhNghiep::count();

        return view('admin.baiviet', compact('baiViet', 'tongBaiViet', 'tongUser', 'tongDoanhNghiep'));
    }
}
