<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TinNhan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TinNhanController extends Controller
{
    /**
     * 📩 Lấy lịch sử tin nhắn giữa người dùng hiện tại và bạn bè
     */
    public function show($id)
    {
        $userId = Auth::id();

        $messages = TinNhan::where(function ($q) use ($userId, $id) {
            $q->where('nguoi_gui_id', $userId)
                ->where('nguoi_nhan_id', $id);
        })->orWhere(function ($q) use ($userId, $id) {
            $q->where('nguoi_gui_id', $id)
                ->where('nguoi_nhan_id', $userId);
        })->orderBy('ngay_gui')->get();

        $friend = User::find($id);
        $html = '';

        foreach ($messages as $m) {
            if ($m->nguoi_gui_id == $userId) {
                $html .= "<div class='msg me' data-msg-id='{$m->id}'>";
                $html .= "<button class='delete-msg-btn' data-id='{$m->id}' title='Xóa tin nhắn'>
                <i class='bi bi-trash'></i>
              </button>";
                if (!empty($m->noi_dung)) {
                    $html .= "<div class='bubble'>{$m->noi_dung}</div>";
                }
                if (!empty($m->hinh_anh)) {
                    $html .= "<img src='" . asset('storage/app/public/' . $m->hinh_anh) . "' class='chat-img'>";
                }
                $html .= "</div>";
            } else {
                // 👤 Tin nhắn người kia gửi (vẫn có avatar)
                $avatar = $friend && $friend->anh_dai_dien
                    ? asset('storage/app/public/' . $friend->anh_dai_dien)
                    : asset('public/uploads/default.png');

                $html .= "<div class='msg you'>";
                $html .= "<img src='{$avatar}' class='avatar'>";

                if (!empty($m->noi_dung)) {
                    $html .= "<div class='bubble'>{$m->noi_dung}</div>";
                }

                if (!empty($m->hinh_anh)) {
                    $html .= "<img src='" . asset('storage/app/public/' . $m->hinh_anh) . "' class='chat-img'>";
                }

                $html .= "</div>";
            }
        }

        return $html ?: '';
    }

    /**
     * 🚀 Gửi tin nhắn mới (cho phép gửi ảnh độc lập với chữ)
     */
    public function send(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|integer',
            'noi_dung' => 'nullable|string',
            'hinh_anh' => 'nullable|image|max:4096',
        ]);

        // 📸 Upload ảnh nếu có
        $path = null;
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('chat-images', 'public');
        }

        // 🚫 Nếu không có nội dung và không có ảnh => bỏ qua
        if (empty($request->noi_dung) && !$path) {
            return response('', 204);
        }

        // 💾 Lưu tin nhắn vào DB
        $msg = TinNhan::create([
            'nguoi_gui_id' => Auth::id(),
            'nguoi_nhan_id' => $request->friend_id,
            'noi_dung' => $request->noi_dung,
            'hinh_anh' => $path,
        ]);

        // 🧱 Trả HTML cho JS hiển thị ngay (❌ không có avatar)
        $html = "<div class='msg me'>";

        if (!empty($msg->noi_dung)) {
            $html .= "<div class='bubble'>{$msg->noi_dung}</div>";
        }

        if (!empty($msg->hinh_anh)) {
            $html .= "<img src='" . asset('storage/app/public/' . $msg->hinh_anh) . "' class='chat-img'>";
        }

        // Không còn avatar
        $html .= "</div>";

        return response($html);
    }

    public function kiemTraMoi()
    {
        $userId = Auth::id();

        $unread = TinNhan::where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->pluck('nguoi_gui_id')
            ->unique()
            ->toArray();

        return response()->json($unread);
    }

    public function danhDauDaDoc($friend_id)
    {
        $userId = Auth::id();

        TinNhan::where('nguoi_gui_id', $friend_id)
            ->where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->update(['da_doc' => true]);

        return response()->json(['success' => true]);
    }

    public function xoaTinNhan($id)
    {
        $userId = Auth::id();

        $msg = TinNhan::find($id);

        if (!$msg) {
            return response()->json(['success' => false, 'message' => 'Tin nhắn không tồn tại'], 404);
        }

        if ($msg->nguoi_gui_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa tin nhắn này'], 403);
        }

        if ($msg->hinh_anh && Storage::disk('public')->exists($msg->hinh_anh)) {
            Storage::disk('public')->delete($msg->hinh_anh);
        }

        $msg->delete();

        return response()->json(['success' => true]);
    }
}
