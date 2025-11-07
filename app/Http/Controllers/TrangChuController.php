<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\BaiViet;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TrangChuController extends Controller
{
    public function index()
    {
        // 🧹 1️⃣ Xóa story hết hạn
        Story::where('thoi_han', '<=', now())->each(function ($story) {
            if ($story->hinh_anh && Storage::disk('public')->exists($story->hinh_anh)) {
                Storage::disk('public')->delete($story->hinh_anh);
            }
            if ($story->video && Storage::disk('public')->exists($story->video)) {
                Storage::disk('public')->delete($story->video);
            }
            $story->delete();
        });

        // 🧩 2️⃣ Lấy danh sách bạn bè đã chấp nhận
        $userId = Auth::id();

        $friendIds = DB::table('ket_ban')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('ban_be_id', $userId);
            })
            ->where('trang_thai', 'chap_nhan')
            ->pluck(DB::raw('CASE WHEN user_id = ' . $userId . ' THEN ban_be_id ELSE user_id END'))
            ->toArray();

        // Thêm chính mình vào danh sách hiển thị
        $friendIds[] = $userId;

        // 🕒 3️⃣ Lấy story của mình + bạn bè (mỗi người chỉ hiện story mới nhất)
        $stories = Story::with('user')
            ->where('thoi_han', '>', now())
            ->whereIn('user_id', $friendIds)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MIN(id)')
                    ->from('story')
                    ->where('thoi_han', '>', now())
                    ->groupBy('user_id');
            })
            ->latest('ngay_dang')
            ->take(10)
            ->get();

        // 🧭 4️⃣ Lấy toàn bộ story của mình + bạn bè (để xem tuần tự trong overlay)
        $allStories = Story::with('user')
            ->where('thoi_han', '>', now())
            ->whereIn('user_id', $friendIds)
            ->orderBy('ngay_dang', 'asc')
            ->get();

        // 📰 5️⃣ Lấy bài viết mới nhất (phân trang)
        $baiviets = BaiViet::with('user')
            ->latest('ngay_dang')
            ->get();

        // ✅ 6️⃣ Trả về view
        return view('trangchu.index', compact('stories', 'baiviets', 'allStories'));
    }
}
