<?php

namespace App\Http\Controllers;

use App\Models\KetBan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class KetBanController extends Controller
{
    // Gửi lời mời kết bạn
    public function send($id)
    {
        $userId = Auth::id();

        if ($userId == $id) {
            return back()->with('error', 'Không thể kết bạn với chính mình.');
        }

        // Kiểm tra đã tồn tại lời mời hoặc quan hệ bạn bè chưa
        $exists = KetBan::where(function ($q) use ($userId, $id) {
            $q->where('user_id', $userId)
                ->where('ban_be_id', $id);
        })
            ->orWhere(function ($q) use ($userId, $id) {
                $q->where('user_id', $id)
                    ->where('ban_be_id', $userId);
            })
            ->first();

        if ($exists) {
            return back()->with('error', 'Đã tồn tại lời mời hoặc bạn bè.');
        }

        KetBan::create([
            'user_id'    => $userId,
            'ban_be_id'  => $id,
            'trang_thai' => 'cho'
        ]);

        return back()->with('success', 'Đã gửi lời mời kết bạn!');
    }

    // Xác nhận lời mời
    public function accept($id)
    {
        $ketBan = KetBan::findOrFail($id);

        // Đảm bảo user hiện tại là người nhận lời mời
        if ($ketBan->ban_be_id != Auth::id()) {
            return back()->with('error', 'Bạn không có quyền chấp nhận lời mời này.');
        }

        $ketBan->update(['trang_thai' => 'chap_nhan']);
        return back()->with('success', 'Đã chấp nhận lời mời.');
    }

    // Từ chối lời mời
    public function decline($id)
    {
        $ketBan = KetBan::findOrFail($id);

        // Đảm bảo user hiện tại là người nhận lời mời
        if ($ketBan->ban_be_id != Auth::id()) {
            return back()->with('error', 'Bạn không có quyền từ chối lời mời này.');
        }

        $ketBan->update(['trang_thai' => 'tu_choi']);
        return back()->with('info', 'Đã từ chối lời mời.');
    }
    public function cancel($id)
    {
        $userId = Auth::id();

        // Tìm mối quan hệ kết bạn 2 chiều
        $relation = KetBan::where(function ($q) use ($userId, $id) {
            $q->where('user_id', $userId)->where('ban_be_id', $id);
        })
            ->orWhere(function ($q) use ($userId, $id) {
                $q->where('user_id', $id)->where('ban_be_id', $userId);
            })
            ->first();

        if (!$relation) {
            return back()->with('error', 'Không tìm thấy mối quan hệ để hủy.');
        }

        $relation->delete();

        return back()->with('success', 'Đã hủy kết bạn hoặc lời mời.');
    }
    public function tatCaLoiMoi()
    {
        $userId = Auth::id();

        $requests = KetBan::where('ban_be_id', $userId)
            ->where('trang_thai', 'cho')
            ->with('user') // Quan hệ tới người gửi lời mời
            ->orderBy('ngay_ket_ban', 'desc')
            ->get();

        return view('ketban.loimoi', compact('requests'));
    }
    public function goiYBanBe()
    {
        $userId = Auth::id();

        // 🧩 Lấy tất cả id đã có quan hệ (đã kết bạn hoặc đang chờ)
        $connectedIds = KetBan::where('user_id', $userId)
            ->orWhere('ban_be_id', $userId)
            ->pluck('user_id')
            ->merge(
                KetBan::where('user_id', $userId)
                    ->orWhere('ban_be_id', $userId)
                    ->pluck('ban_be_id')
            )
            ->unique()
            ->push($userId)
            ->values()
            ->toArray();

        // 🧩 Lấy danh sách bạn bè của tôi (đã chấp nhận)
        $myFriends = KetBan::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('ban_be_id', $userId);
        })
            ->where('trang_thai', 'chap_nhan')
            ->get()
            ->map(function ($r) use ($userId) {
                return $r->user_id == $userId ? $r->ban_be_id : $r->user_id;
            })
            ->unique()
            ->values();

        // 🧩 Tìm “bạn của bạn”
        $mutualIds = KetBan::where(function ($q) use ($myFriends) {
            $q->whereIn('user_id', $myFriends)
                ->orWhereIn('ban_be_id', $myFriends);
        })
            ->where('trang_thai', 'chap_nhan')
            ->get()
            ->flatMap(function ($r) {
                return [$r->user_id, $r->ban_be_id];
            })
            ->unique()
            ->diff($myFriends)
            ->diff([$userId])
            ->values();

        // 🧩 Ưu tiên bạn của bạn trước
        $suggestions = User::whereIn('id', $mutualIds)
            ->whereNotIn('id', $connectedIds)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        // 🧩 Nếu chưa đủ 10, thêm người cùng khu vực
        if ($suggestions->count() < 10) {
            $myAddress = Auth::user()->dia_chi;

            $extra = User::whereNotIn('id', array_merge($connectedIds, $suggestions->pluck('id')->toArray()))
                ->when($myAddress, function ($q) use ($myAddress) {
                    $q->where('dia_chi', $myAddress);
                })
                ->inRandomOrder()
                ->limit(10 - $suggestions->count())
                ->get();

            $suggestions = $suggestions->merge($extra);
        }

        // 🧩 Nếu vẫn chưa đủ → lấy random người còn lại
        if ($suggestions->count() < 10) {
            $remaining = User::whereNotIn('id', array_merge($connectedIds, $suggestions->pluck('id')->toArray()))
                ->inRandomOrder()
                ->limit(10 - $suggestions->count())
                ->get();

            $suggestions = $suggestions->merge($remaining);
        }

        // 🧩 Đảm bảo không trùng, và chỉ lấy tối đa 10 người
        $suggestions = $suggestions->unique('id')->take(10)->values();

        // 🧩 Tính số bạn chung
        $suggestions = $suggestions->map(function ($user) use ($myFriends) {
            $userFriendIds = KetBan::where('trang_thai', 'chap_nhan')
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('ban_be_id', $user->id);
                })
                ->get()
                ->map(function ($r) use ($user) {
                    return $r->user_id == $user->id ? $r->ban_be_id : $r->user_id;
                })
                ->unique();

            $user->mutual_count = $myFriends->intersect($userFriendIds)->count();
            return $user;
        })
            ->sortByDesc('mutual_count')
            ->values();

        return view('ketban.goi_y', compact('suggestions'));
    }
    public function tatCaBanBe()
    {
        $userId = Auth::id();

        // Lấy tất cả quan hệ đã chấp nhận
        $friends = KetBan::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('ban_be_id', $userId);
        })
            ->where('trang_thai', 'chap_nhan')
            ->with(['user', 'banBe'])
            ->orderBy('ngay_ket_ban', 'desc')
            ->get();

        // Chuyển mỗi bản ghi về đối tượng bạn bè thực sự
        $friendList = $friends->map(function ($item) use ($userId) {
            return $item->user_id == $userId ? $item->banBe : $item->user;
        });

        return view('ketban.ban_be', compact('friendList'));
    }
}
