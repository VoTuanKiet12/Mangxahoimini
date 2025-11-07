@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/nhom.css') }}">
@section('title', 'Danh sách nhóm của tôi')

@section('content')
<div class="container-nhom">
    <h3>DANH SÁCH NHÓM CỦA TÔI</h3>
    @if (!empty($loiMoi) && $loiMoi->isNotEmpty())
    <div class="invite-notify-nhom">
        <h4>Bạn có {{ $loiMoi->count() }} lời mời tham gia nhóm</h4>
        @foreach ($loiMoi as $moi)
        <div class="invite-item-nhom">
            <div class="invite-info-nhom">
                <strong>{{ $moi->nhom->ten_nhom }}</strong>
                <span>— Người mời: {{ $moi->nhom->chuNhom->name ?? 'Không rõ' }}</span>
            </div>
            <div class="invite-actions-nhom">
                <form action="{{ route('nhom.accept', $moi->nhom_id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-accept">Chấp nhận</button>
                </form>
                <form action="{{ route('nhom.reject', $moi->nhom_id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-reject">Từ chối</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif


    @if ($dsNhom->isEmpty())
    <div class="empty-box">
        <i class="bi bi-people-fill"></i>
        <p>Bạn chưa tham gia hoặc tạo nhóm nào.</p>
        <a href="{{ route('nhom.create') }}" class="btn-create">+ Tạo nhóm mới</a>

    </div>
    @else
    <a href="{{ route('nhom.create') }}" class="btn-create">+ Tạo nhóm mới</a>
    <div class="group-grid">
        @foreach ($dsNhom as $nhom)
        <div class="group-card">
            @if ($nhom->anh_bia)
            <img src="{{ asset('storage/app/public/' . $nhom->anh_bia) }}" alt="Ảnh bìa nhóm" class="group-img">
            @else
            <div class="group-img" style="display:flex;align-items:center;justify-content:center;color:#aaa;">
                <i class="bi bi-image" style="font-size:40px;"></i>
            </div>
            @endif

            <div class="group-content">
                <div>
                    <div class="group-title">{{ $nhom->ten_nhom }}</div>
                    <div class="group-desc">{{ $nhom->mo_ta ?? 'Không có mô tả' }}</div>
                    <div class="group-badge">{{ ucfirst($nhom->che_do) }}</div>
                    <div class="group-creator">
                        Người tạo: <strong>{{ $nhom->chuNhom->name ?? 'Không rõ' }}</strong>
                    </div>
                </div>
                <div class="group-btn">
                    <a href="{{ route('nhom.show', $nhom->id) }}" class="btn-view">Vào nhóm</a>
                    @php
                    $thanhVien = \App\Models\ThanhVienNhom::where('nhom_id', $nhom->id)
                    ->where('user_id', auth()->id())
                    ->first();
                    @endphp

                    @if ($nhom->che_do === 'cong_khai' || ($thanhVien && in_array($thanhVien->vai_tro, ['chu_nhom', 'quan_tri_vien'])))
                    <button type="button" class="btn-invite" onclick="openInvitePopup({{ $nhom->id }})">Mời bạn</button>
                    @endif

                    @if ($nhom->nguoi_tao_id === auth()->id())
                    <div class="dropdown-nhom">
                        <button class="btn-more" onclick="openDropdown({{ $nhom->id }})">⋯</button>
                    </div>
                    @endif


                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection


<div id="inviteOverlaynhom" class="invite-overlaynhom">
    <div class="invite-boxnhom">
        <h4>Mời bạn vào nhóm</h4>
        <div id="friendListnhom"></div>
    </div>
</div>

<div id="overlayMenu" class="overlay-nhom hidden">
    <div class="overlay-box">
        <p>Quản lý nhóm</p>
        <a href="#" id="editLink" class="overlay-item edit">Chỉnh sửa</a>
        <form id="deleteForm" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa nhóm này không?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="overlay-item delete">Xóa nhóm</button>
        </form>
    </div>
</div>

<script>
    let currentGroupId = null;

    // Hàm mở popup + tải danh sách bạn bè
    function openInvitePopup(groupId) {
        currentGroupId = groupId;
        const overlay = document.getElementById('inviteOverlaynhom');
        const list = document.getElementById('friendListnhom');
        overlay.style.display = 'flex';

        // Lấy danh sách bạn bè có thể mời (AJAX)
        fetch(`{{ url('nhom') }}/${groupId}/danh-sach-moi`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                if (data.length === 0) {
                    list.innerHTML = '<p>Bạn không còn bạn bè nào để mời.</p>';
                    return;
                }
                data.forEach(friend => {
                    const item = document.createElement('div');
                    item.classList.add('friend-itemnhom');
                    item.innerHTML = `
                        <span>${friend.name}</span>
                        <button onclick="inviteFriend(${friend.id})">Mời</button>
                    `;
                    list.appendChild(item);
                });
            });

        // Click ra ngoài để đóng
        overlay.addEventListener('click', function(event) {
            if (event.target === overlay) closeInvitePopup();
        });
    }

    function closeInvitePopup() {
        document.getElementById('inviteOverlaynhom').style.display = 'none';
    }

    function inviteFriend(friendId) {
        const inviteUrl = `{{ url('nhom') }}/${currentGroupId}/moi-ban`;

        fetch(inviteUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    friend_id: friendId
                })
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    alert(data.success);
                    closeInvitePopup();
                } else {
                    alert(data.error || 'Đã có lỗi xảy ra.');
                }
            })
            .catch(err => {
                console.error('Lỗi fetch:', err);
                alert('Lỗi khi gửi lời mời.');
            });
    }
</script>
<script>
    function openDropdown(groupId) {
        const overlay = document.getElementById('overlayMenu');
        const editLink = document.getElementById('editLink');
        const deleteForm = document.getElementById('deleteForm');

        editLink.href = `{{ url('nhom') }}/${groupId}/edit`;

        deleteForm.action = `{{ url('nhom') }}/${groupId}/xoa`;

        overlay.classList.add('show');
    }


    function closeDropdown() {
        document.getElementById('overlayMenu').classList.remove('show');
    }

    // Click ra ngoài để đóng
    window.addEventListener('click', function(e) {
        const overlay = document.getElementById('overlayMenu');
        if (e.target === overlay) closeDropdown();
    });
</script>