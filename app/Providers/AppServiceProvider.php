<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\KetBan;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\TinNhan;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
        View::composer('layouts.sidebar-right', function ($view) {
            if (!Auth::check()) {
                $view->with([
                    'requests'    => collect(),
                    'suggestions' => collect(),
                    'friends'     => collect(),
                ]);
                return;
            }

            $userId = Auth::id();

            /* ===================================================
             * 1️⃣ Lời mời kết bạn (người khác gửi tới mình, trạng thái "chờ")
             * =================================================== */
            $requests = KetBan::where('ban_be_id', $userId)
                ->where('trang_thai', 'cho')
                ->with('user')
                ->orderBy('ngay_ket_ban', 'desc')
                ->get();

            /* ===================================================
             * 2️⃣ Gợi ý bạn bè thông minh
             * =================================================== */


            $connectedIds = KetBan::where('user_id', $userId)
                ->orWhere('ban_be_id', $userId)
                ->pluck('user_id')
                ->merge(
                    KetBan::where('user_id', $userId)
                        ->orWhere('ban_be_id', $userId)
                        ->pluck('ban_be_id')
                )
                ->unique()
                ->push($userId) // loại chính mình
                ->values()
                ->toArray();

            // ✅ Lấy danh sách bạn bè của tôi (đã chấp nhận)
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

            // ✅ Nếu chưa có bạn bè → gợi ý ngẫu nhiên
            if ($myFriends->isEmpty()) {
                $suggestions = User::where('id', '!=', $userId)
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();
            } else {
                // ✅ Tìm “bạn của bạn”
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

                // ✅ Ưu tiên “bạn của bạn” trước
                $suggestions = User::whereIn('id', $mutualIds)
                    ->whereNotIn('id', $connectedIds)
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();

                // ✅ Nếu chưa đủ 4 người, thêm người cùng khu vực hoặc random
                if ($suggestions->count() < 4) {
                    $myAddress = Auth::user()->dia_chi;

                    $extra = User::whereNotIn('id', $connectedIds)
                        ->when($myAddress, function ($q) use ($myAddress) {
                            $q->where('dia_chi', $myAddress);
                        })
                        ->inRandomOrder()
                        ->limit(4 - $suggestions->count())
                        ->get();

                    if ($extra->isEmpty()) {
                        $extra = User::whereNotIn('id', $connectedIds)
                            ->inRandomOrder()
                            ->limit(4 - $suggestions->count())
                            ->get();
                    }

                    $suggestions = $suggestions->merge($extra);
                }

                // ✅ Tính số “bạn chung” cho từng người gợi ý
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

                    $user->mutual_count = collect($myFriends)->intersect($userFriendIds)->count();
                    return $user;
                })
                    ->sortByDesc('mutual_count')
                    ->values();
            }

            /* ===================================================
             * 3️⃣ Danh sách bạn bè (đã chấp nhận)
             * =================================================== */
            $friends = KetBan::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('ban_be_id', $userId);
            })
                ->where('trang_thai', 'chap_nhan')
                ->with(['user', 'banBe'])
                ->get()
                ->map(function ($fr) use ($userId) {
                    $banbe = $fr->user_id == $userId ? $fr->banBe : $fr->user;

                    // 🔹 Lấy tin nhắn gần nhất giữa tôi và bạn
                    $lastMessage = \App\Models\TinNhan::where(function ($q) use ($userId, $banbe) {
                        $q->where('nguoi_gui_id', $userId)
                            ->where('nguoi_nhan_id', $banbe->id);
                    })
                        ->orWhere(function ($q) use ($userId, $banbe) {
                            $q->where('nguoi_gui_id', $banbe->id)
                                ->where('nguoi_nhan_id', $userId);
                        })
                        ->latest('ngay_gui')
                        ->select('ngay_gui') // ✅ chỉ lấy thời gian, giảm tải
                        ->first();

                    // 🔹 Gắn thông tin bổ sung
                    $fr->banbe = $banbe;
                    $fr->last_message_time = $lastMessage ? $lastMessage->ngay_gui : null;
                    return $fr;
                })
                ->sortByDesc(function ($fr) {
                    // Nếu chưa có tin nhắn, đưa xuống cuối
                    return $fr->last_message_time ?? \Carbon\Carbon::createFromTimestamp(0);
                })
                ->values();

            $view->with(compact('requests', 'suggestions', 'friends'));
        });

        // 🕓 Cấu hình múi giờ + ngôn ngữ
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        Carbon::setLocale('vi');

        // 🧩 Phân quyền admin
        Gate::define('access-admin', function (User $user) {
            return $user->role === 'admin';
        });
    }

    public function register()
    {
        //
    }
}
