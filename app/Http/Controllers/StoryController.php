<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StoryController extends Controller
{
    /**
     * Lưu story mới (ảnh hoặc video)
     */
    public function store(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đăng story!');
        }

        // Xác thực dữ liệu đầu vào
        $request->validate([
            'noi_dung' => 'nullable|string|max:255',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
            'video'    => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:102400',
        ]);

        // Tạo mới story
        $story = new Story();
        $story->user_id = Auth::id();
        $story->noi_dung = $request->noi_dung;

        // Upload ảnh (nếu có)
        if ($request->hasFile('hinh_anh')) {
            $story->hinh_anh = $request->file('hinh_anh')->store('story', 'public');
        }

        // Upload video (nếu có)
        if ($request->hasFile('video')) {
            $story->video = $request->file('video')->store('story', 'public');
        }

        // Story tự hết hạn sau 24h
        $story->thoi_han = Carbon::now()->addHours(24);
        $story->save();

        return redirect()->back()->with('success', 'Đã đăng tin thành công!');
    }

    /**
     * Dọn dẹp story hết hạn (gọi thủ công hoặc chạy cron)
     */
    public function cleanExpired()
    {
        $expiredStories = Story::where('thoi_han', '<=', Carbon::now())->get();
        $count = $expiredStories->count();

        if ($count === 0) {
            return redirect()->back()->with('info', 'Không có story nào hết hạn.');
        }

        foreach ($expiredStories as $story) {
            // Xóa file ảnh/video nếu có
            if ($story->hinh_anh && Storage::disk('public')->exists($story->hinh_anh)) {
                Storage::disk('public')->delete($story->hinh_anh);
            }
            if ($story->video && Storage::disk('public')->exists($story->video)) {
                Storage::disk('public')->delete($story->video);
            }

            $story->delete();
        }

        return redirect()->back()->with('success', "Đã xóa {$count} story hết hạn!");
    }
    public function destroy($id)
    {
        $story = Story::findOrFail($id);

        if ($story->user_id !== auth()->id()) {
            abort(403, 'Không có quyền xóa story này.');
        }

        if ($story->hinh_anh) {
            Storage::delete('public/' . $story->hinh_anh);
        }
        if ($story->video) {
            Storage::delete('public/' . $story->video);
        }

        $story->delete();

        return response()->json(['success' => true]);
    }
}
