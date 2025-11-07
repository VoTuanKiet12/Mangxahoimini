<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nhom;
use App\Models\KetBan;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\TinNhanNhom;


class NhomController extends Controller
{
    /** Danh sách nhóm của tôi */
    public function index()
    {
        $user = auth()->user();

        // 🔹 Chỉ lấy những nhóm mà user đã "tham_gia"
        $dsNhom = $user->nhom()
            ->with('chuNhom')
            ->wherePivot('trang_thai', 'tham_gia')
            ->get()
            ->merge(
                // 🔹 Cộng thêm các nhóm user là người tạo (chủ nhóm)
                $user->nhomDaTao
            )
            ->unique('id');

        // === Lấy danh sách bạn bè thật sự ===
        $friendIds1 = KetBan::where('user_id', $user->id)
            ->where('trang_thai', 'chap_nhan')
            ->pluck('ban_be_id');
        $friendIds2 = KetBan::where('ban_be_id', $user->id)
            ->where('trang_thai', 'chap_nhan')
            ->pluck('user_id');

        $friendIds = $friendIds1->merge($friendIds2)->unique();

        // Lấy toàn bộ bạn bè
        $friends = User::whereIn('id', $friendIds)->get();

        // 🔹 Danh sách lời mời đang chờ user (nếu có)
        $loiMoi = ThanhVienNhom::where('user_id', $user->id)
            ->where('trang_thai', 'cho_duyet')
            ->with('nhom.chuNhom')
            ->get();

        // Trả về view
        return view('nhom.index', compact('dsNhom', 'friends', 'loiMoi'));
    }


    /** Trang tạo nhóm */
    public function create()
    {
        return view('nhom.create');
    }

    /** Lưu nhóm mới */
    public function store(Request $request)
    {
        $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'che_do' => 'required|in:cong_khai,kin',
            'anh_bia' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $duongDanAnh = null;
        if ($request->hasFile('anh_bia')) {
            $duongDanAnh = $request->file('anh_bia')->store('nhom-bia', 'public');
        }

        // Tạo nhóm
        $nhom = Nhom::create([
            'ten_nhom' => $request->ten_nhom,
            'mo_ta' => $request->mo_ta,
            'anh_bia' => $duongDanAnh,
            'nguoi_tao_id' => Auth::id(),
            'che_do' => $request->che_do,
        ]);

        // Người tạo là chủ nhóm
        ThanhVienNhom::create([
            'nhom_id' => $nhom->id,
            'user_id' => Auth::id(),
            'vai_tro' => 'chu_nhom',
            'trang_thai' => 'tham_gia',
        ]);

        return redirect()->route('nhom.index')->with('success', 'Tạo nhóm thành công!');
    }

    /** Trang chi tiết nhóm */
    public function show($id)
    {
        $nhom = Nhom::with(['chuNhom', 'users'])->findOrFail($id);

        // Nếu nhóm kín mà user không thuộc nhóm → chặn
        if ($nhom->che_do === 'kin' && !$nhom->users->contains(auth()->id())) {
            abort(403, 'Bạn không có quyền xem nhóm này');
        }

        return view('nhom.show', compact('nhom'));
    }

    /** Mời bạn bè vào nhóm */
    /** Mời bạn bè vào nhóm */
    public function inviteFriend(Request $request, $id)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id'
        ]);

        $user = auth()->user();
        $nhom = Nhom::with('users')->findOrFail($id);

        // ✅ Kiểm tra người mời có trong nhóm không
        $vaiTro = ThanhVienNhom::where('nhom_id', $nhom->id)
            ->where('user_id', $user->id)
            ->value('vai_tro');

        if (!$vaiTro) {
            return response()->json(['error' => 'Bạn không thuộc nhóm này!'], 403);
        }

        // ✅ Nếu nhóm kín → chỉ chủ nhóm hoặc quản trị viên mới được mời
        if ($nhom->che_do === 'kin' && !in_array($vaiTro, ['chu_nhom', 'quan_tri_vien'])) {
            return response()->json(['error' => 'Chỉ chủ nhóm hoặc quản trị viên mới có thể mời thành viên trong nhóm kín!'], 403);
        }

        // 🔹 Kiểm tra nếu người này đã tham gia hoặc đang được mời
        $daTonTai = ThanhVienNhom::where('nhom_id', $nhom->id)
            ->where('user_id', $request->friend_id)
            ->whereIn('trang_thai', ['tham_gia', 'cho_duyet'])
            ->exists();

        if ($daTonTai) {
            return response()->json(['error' => 'Người này đã ở trong nhóm hoặc đang được mời!'], 409);
        }

        // 🔹 Nếu từng từ chối lời mời trước → xóa để mời lại
        ThanhVienNhom::where('nhom_id', $nhom->id)
            ->where('user_id', $request->friend_id)
            ->where('trang_thai', 'tu_choi')
            ->delete();

        // 🔹 Kiểm tra có phải bạn bè thật
        $isFriend = KetBan::where(function ($q) use ($user, $request) {
            $q->where('user_id', $user->id)->where('ban_be_id', $request->friend_id);
        })
            ->orWhere(function ($q) use ($user, $request) {
                $q->where('ban_be_id', $user->id)->where('user_id', $request->friend_id);
            })
            ->where('trang_thai', 'chap_nhan')
            ->exists();

        if (!$isFriend) {
            return response()->json(['error' => 'Chỉ có thể mời bạn bè!'], 403);
        }

        // ✅ Tạo lời mời mới
        ThanhVienNhom::create([
            'nhom_id' => $nhom->id,
            'user_id' => $request->friend_id,
            'vai_tro' => 'thanh_vien',
            'trang_thai' => 'cho_duyet'
        ]);

        return response()->json(['success' => 'Đã gửi lời mời thành công!']);
    }


    public function acceptInvite($id)
    {
        $user = auth()->user();

        $thanhVien = ThanhVienNhom::where('nhom_id', $id)
            ->where('user_id', $user->id)
            ->where('trang_thai', 'cho_duyet')
            ->first();

        if (!$thanhVien) {
            return redirect()->back()->with('error', 'Lời mời không tồn tại hoặc đã xử lý.');
        }

        $thanhVien->update(['trang_thai' => 'tham_gia']);

        return redirect()->back()->with('success', 'Bạn đã tham gia nhóm thành công!');
    }

    // Người dùng từ chối lời mời
    public function rejectInvite($id)
    {
        $user = auth()->user();

        $thanhVien = ThanhVienNhom::where('nhom_id', $id)
            ->where('user_id', $user->id)
            ->where('trang_thai', 'cho_duyet')
            ->first();

        if (!$thanhVien) {
            return redirect()->back()->with('error', 'Lời mời không tồn tại hoặc đã xử lý.');
        }

        $thanhVien->delete();

        return redirect()->back()->with('success', 'Bạn đã từ chối lời mời và lời mời đã được xóa.');
    }
    public function getAvailableFriends($id)
    {
        $user = auth()->user();
        $nhom = Nhom::with('users')->findOrFail($id);

        // Lấy bạn bè thật
        $friendIds1 = KetBan::where('user_id', $user->id)
            ->where('trang_thai', 'chap_nhan')
            ->pluck('ban_be_id');
        $friendIds2 = KetBan::where('ban_be_id', $user->id)
            ->where('trang_thai', 'chap_nhan')
            ->pluck('user_id');
        $friendIds = $friendIds1->merge($friendIds2)->unique();

        // Lấy id thành viên nhóm (đã tham gia hoặc đang chờ)
        $members = ThanhVienNhom::where('nhom_id', $nhom->id)
            ->whereIn('trang_thai', ['tham_gia', 'cho_duyet'])
            ->pluck('user_id')
            ->toArray();

        // Lọc ra bạn bè chưa ở nhóm
        $available = User::whereIn('id', $friendIds)
            ->whereNotIn('id', $members)
            ->get(['id', 'name']);

        return response()->json($available);
    }
    public function destroy($id)
    {
        $user = auth()->user();
        $nhom = Nhom::findOrFail($id);

        // ✅ Chỉ chủ nhóm mới được phép xóa
        if ($nhom->nguoi_tao_id !== $user->id) {
            return redirect()->back()->with('error', 'Chỉ chủ nhóm mới có thể xóa nhóm này!');
        }

        // ✅ Xóa toàn bộ thành viên nhóm
        ThanhVienNhom::where('nhom_id', $nhom->id)->delete();

        // ✅ Nếu có ảnh bìa thì xóa luôn khỏi storage
        if (!empty($nhom->anh_bia) && Storage::disk('public')->exists($nhom->anh_bia)) {
            Storage::disk('public')->delete($nhom->anh_bia);
        }

        // ✅ Cuối cùng xóa nhóm
        $nhom->delete();

        return redirect()->route('nhom.index')->with('success', 'Đã xóa nhóm và ảnh bìa thành công!');
    }
    public function leave($id)
    {
        $user = auth()->user();
        $nhom = Nhom::findOrFail($id);

        // Không cho chủ nhóm rời nhóm
        if ($nhom->nguoi_tao_id === $user->id) {
            return redirect()->back()->with('error', 'Chủ nhóm không thể rời khỏi nhóm của mình!');
        }

        // Kiểm tra thành viên tồn tại
        $thanhVien = ThanhVienNhom::where('nhom_id', $id)
            ->where('user_id', $user->id)
            ->where('trang_thai', 'tham_gia')
            ->first();

        if (!$thanhVien) {
            return redirect()->back()->with('error', 'Bạn không phải là thành viên của nhóm này!');
        }

        // Xóa thành viên khỏi nhóm
        $thanhVien->delete();

        return redirect()->route('nhom.index')->with('success', 'Bạn đã rời khỏi nhóm thành công.');
    }
    public function edit($id)
    {
        $nhom = Nhom::findOrFail($id);

        if ($nhom->nguoi_tao_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này.');
        }

        return view('nhom.edit', compact('nhom'));
    }
    public function update(Request $request, $id)
    {
        $nhom = Nhom::findOrFail($id);

        if ($nhom->nguoi_tao_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này.');
        }

        $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'che_do' => 'required|in:cong_khai,kin',
            'anh_bia' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Nếu có ảnh mới, xóa ảnh cũ
        if ($request->hasFile('anh_bia')) {
            if ($nhom->anh_bia && Storage::disk('public')->exists($nhom->anh_bia)) {
                Storage::disk('public')->delete($nhom->anh_bia);
            }
            $duongDanAnh = $request->file('anh_bia')->store('nhom-bia', 'public');
            $nhom->anh_bia = $duongDanAnh;
        }

        $nhom->ten_nhom = $request->ten_nhom;
        $nhom->mo_ta = $request->mo_ta;
        $nhom->che_do = $request->che_do;
        $nhom->save();

        return redirect()->route('nhom.index')->with('success', 'Cập nhật thông tin nhóm thành công!');
    }
    public function kickMember($nhomId, $userId)
    {
        $nhom = Nhom::with('users')->findOrFail($nhomId);
        $currentUser = auth()->user();

        $currentVaiTro = ThanhVienNhom::where('nhom_id', $nhomId)
            ->where('user_id', $currentUser->id)
            ->value('vai_tro');

        // Chỉ chu_nhom hoặc quan_tri_vien mới kick được
        if (!in_array($currentVaiTro, ['chu_nhom', 'quan_tri_vien'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền kick thành viên!');
        }

        // Không kick chủ nhóm
        $vaiTroNguoiBiKick = ThanhVienNhom::where('nhom_id', $nhomId)
            ->where('user_id', $userId)
            ->value('vai_tro');

        if ($vaiTroNguoiBiKick === 'chu_nhom') {
            return redirect()->back()->with('error', 'Không thể kick chủ nhóm!');
        }

        // Xóa thành viên
        ThanhVienNhom::where('nhom_id', $nhomId)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->back()->with('success', 'Đã kick thành viên thành công!');
    }
    public function quanlynhom($id)
    {
        $nhom = Nhom::with(['users'])->findOrFail($id);

        // Kiểm tra quyền: chỉ chủ nhóm hoặc quản trị viên mới vào được
        $vaiTro = ThanhVienNhom::where('nhom_id', $id)
            ->where('user_id', auth()->id())
            ->value('vai_tro');

        if (!in_array($vaiTro, ['chu_nhom', 'quan_tri_vien'])) {
            abort(403, 'Bạn không có quyền quản lý nhóm này.');
        }

        return view('nhom.quanlynhom', compact('nhom', 'vaiTro'));
    }

    public function updateMemberRole($nhomId, $userId, Request $request)
    {
        $currentUser = auth()->user();
        $currentVaiTro = ThanhVienNhom::where('nhom_id', $nhomId)
            ->where('user_id', $currentUser->id)
            ->value('vai_tro');

        if (!in_array($currentVaiTro, ['chu_nhom', 'quan_tri_vien'])) {
            return back()->with('error', 'Bạn không có quyền thay đổi vai trò!');
        }

        $thanhVien = ThanhVienNhom::where('nhom_id', $nhomId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($thanhVien->vai_tro === 'chu_nhom') {
            return back()->with('error', 'Không thể thay đổi vai trò của chủ nhóm!');
        }

        $thanhVien->update(['vai_tro' => $request->vai_tro]);

        return back()->with('success', 'Cập nhật vai trò thành công!');
    }


    public function messages($id)
    {
        // ✅ Trả về trang hiển thị chat (view)
        $nhom = Nhom::findOrFail($id);
        return view('nhom.center_messages', compact('nhom'));
    }

    public function getMessages($id)
    {
        $userId = Auth::id();

        $messages = TinNhanNhom::where('nhom_id', $id)
            ->with('nguoiGui:id,name')
            ->orderBy('ngay_gui', 'asc')
            ->get()
            ->map(function ($msg) use ($userId) {
                return [
                    'id' => $msg->id,
                    'noi_dung' => $msg->noi_dung,
                    'anh' => $msg->anh,
                    'ngay_gui' => $msg->ngay_gui,
                    'nguoi_gui_id' => $msg->nguoi_gui_id,
                    'nguoi_gui' => $msg->nguoiGui,
                    'co_the_xoa' => $msg->nguoi_gui_id == $userId,
                ];
            });

        return response()->json($messages);
    }




    public function sendMessage(Request $request, $id)
    {
        // Cho phép cả ảnh hoặc nội dung
        $request->validate([
            'noi_dung' => 'nullable|string|max:1000',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        // Nếu không có cả 2 thì báo lỗi
        if (!$request->noi_dung && !$request->hasFile('anh')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải nhập nội dung hoặc chọn ảnh!'
            ], 422);
        }

        $duongDanAnh = null;

        // ✅ Lưu ảnh (nếu có)
        if ($request->hasFile('anh')) {
            $duongDanAnh = $request->file('anh')->store('tin-nhan-anh', 'public');
        }

        // ✅ Lưu tin nhắn vào DB
        TinNhanNhom::create([
            'nhom_id' => $id,
            'nguoi_gui_id' => auth()->id(),
            'noi_dung' => $request->noi_dung ?: null,
            'anh' => $duongDanAnh,
        ]);

        return response()->json(['success' => true]);
    }


    public function deleteGroupMessage($id)
    {
        try {
            $message = TinNhanNhom::with('nhom')->findOrFail($id);
            $user = auth()->user();

            // 🔹 Kiểm tra thành viên có trong nhóm không
            $thanhVien = ThanhVienNhom::where('nhom_id', $message->nhom_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$thanhVien) {
                return response()->json(['success' => false, 'error' => 'Bạn không thuộc nhóm này!'], 403);
            }

            // 🔹 Chỉ cho phép xóa nếu là chính người gửi
            if ($message->nguoi_gui_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chỉ người gửi mới có thể xóa tin nhắn của mình!'
                ], 403);
            }

            // 🔹 Xóa ảnh nếu có
            if ($message->anh && Storage::disk('public')->exists($message->anh)) {
                Storage::disk('public')->delete($message->anh);
            }

            // 🔹 Xóa tin nhắn
            $message->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            \Log::error('❌ Lỗi xóa tin nhắn nhóm: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Lỗi hệ thống!'], 500);
        }
    }
}
