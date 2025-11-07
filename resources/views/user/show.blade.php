@extends('layouts.app')

@section('title', 'Trang cá nhân - ' . ($user->name ?? $user->username))

@section('content')
<link rel="stylesheet" href="{{ asset('public/css/profile.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/index.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/binhluan.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/like.css') }}">

<div class="profile-container">

    {{-- Ảnh bìa --}}
    <div class="cover-photo">
        <img src="{{ $user->anh_bia 
        ? asset('storage/app/public/' . $user->anh_bia) 
        : asset('public/uploads/default.png') }}"
            alt="Ảnh bìa">

        @if(Auth::check() && Auth::id() === $user->id)
        <form method="POST" action="{{ route('user.update.cover') }}" enctype="multipart/form-data" class="update-cover-form">
            @csrf
            <label for="anh_bia_input" class="btn-change-cover">
                <i class="bi bi-camera"></i> Đổi ảnh bìa
            </label>
            <input type="file" id="anh_bia_input" name="anh_bia" accept="image/*" onchange="this.form.submit()" hidden>
        </form>
        @endif
    </div>

    {{-- Ảnh đại diện + tên --}}
    <div class="profile-header">
        <div class="avatar-wrapper">
            <img src="{{ $user->anh_dai_dien 
            ? asset('storage/app/public/' . $user->anh_dai_dien) 
            : asset('public/uploads/default.png') }}"
                alt="Ảnh đại diện" class="avatar">

            @if(Auth::check() && Auth::id() === $user->id)
            <form method="POST" action="{{ route('user.update.avatar') }}" enctype="multipart/form-data" class="update-avatar-form">
                @csrf
                <label for="anh_dai_dien_input" class="btn-change-avatar">
                    <i class="bi bi-camera"></i>
                </label>
                <input type="file" id="anh_dai_dien_input" name="anh_dai_dien" accept="image/*" onchange="this.form.submit()" hidden>
            </form>
            @endif
        </div>

        <div class="profile-info">
            <h2>{{ $user->name ?? $user->username }}</h2>

            {{-- ✅ Nút bạn bè --}}
            @if(Auth::check() && Auth::id() !== $user->id)
            <div class="friend-action">
                @if(is_null($friendStatus))
                {{-- Chưa kết bạn --}}
                <form method="POST" action="{{ route('ketban.send', $user->id) }}">
                    @csrf
                    <button type="submit" class="btn-add-friendpr">Kết bạn</button>
                </form>
                @elseif($friendStatus === 'cho')
                <form method="POST" action="{{ route('ketban.accept', $user->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-acceptpr">Xác nhận</button>
                </form>
                <form method="POST" action="{{ route('ketban.decline', $user->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-declinepr">Từ chối</button>
                </form>
                @elseif($friendStatus === 'chap_nhan')
                <form method="POST" action="{{ route('ketban.cancel', $user->id) }}">
                    @csrf
                    <button type="submit" class="btn-unfriendpr">Hủy kết bạn</button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Thông tin cá nhân --}}
    <div class="user-details">
        <h3>Thông tin cá nhân</h3>
        <ul>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Số điện thoại:</strong> {{ $user->so_dien_thoai ?? 'Chưa cập nhật' }}</li>
            <li><strong>Địa chỉ:</strong> {{ $user->dia_chi ?? 'Chưa cập nhật' }}</li>
            <li><strong>Ngày sinh:</strong>
                {{ optional($user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh) : null)?->format('d/m/Y') ?? 'Chưa cập nhật' }}
            </li>
            <li><strong>Tham gia từ:</strong> {{ $user->created_at?->format('d/m/Y') ?? 'Không rõ' }}</li>
        </ul>
    </div>

    {{-- =============================== --}}
    {{-- DANH SÁCH BÀI VIẾT CỦA NGƯỜI DÙNG --}}
    {{-- =============================== --}}
    <div class="user-posts">
        <h3>Bài viết của {{ $user->name ?? $user->username }}</h3>

        @forelse($baiviets as $post)
        <div class="post-box">

            {{-- Header bài viết --}}
            <div class="post-header">
                <a href="{{ route('user.show', $post->user->id) }}"
                    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <img src="{{ $post->user->anh_dai_dien
                        ? asset('storage/app/public/' . $post->user->anh_dai_dien)
                        : asset('public/uploads/default.png') }}" class="avatar-bv"
                        alt="{{ $post->user->name ?? $post->user->username }}">

                    <strong>{{ $post->user->name ?? $post->user->username }}</strong>
                </a>

                <span class="ngay-tao">
                    {{ \Carbon\Carbon::parse($post->ngay_dang ?? $post->created_at)->format('d/m/Y') }}
                </span>

                {{-- Xóa bài viết nếu là chủ sở hữu --}}
                @if(Auth::id() === $post->user->id)
                <div class="xoa_bd">
                    <form method="POST" action="{{ route('baiviet.destroy', $post->id) }}"
                        onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?')" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn_xoabd" type="submit">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Nội dung --}}
            <p>{{ $post->noi_dung }}</p>

            {{-- Ảnh (nhiều ảnh) --}}
            @if(!empty($post->hinh_anh))
            @php
            $images = is_array($post->hinh_anh) ? $post->hinh_anh : json_decode($post->hinh_anh, true);
            $images = $images ?? [];
            $count = count($images);
            $displayCount = min($count, 4);
            $wrapperClass = 'image-gallery count-' . $displayCount;
            @endphp

            @if($count > 0)
            <div class="{{ $wrapperClass }}">
                @for($i = 0; $i < $displayCount; $i++)
                    <div class="image-item">
                    <img src="{{ asset('storage/app/public/' . $images[$i]) }}" alt="Ảnh bài viết">
                    @if($i === 3 && $count > 4)
                    <div class="more-overlay">+{{ $count - 4 }}</div>
                    @endif
            </div>
            @endfor
        </div>
        @endif
        @endif

        {{-- Video --}}
        @if(!empty($post->video))
        <video class="auto-play-video" controls muted>
            <source src="{{ asset('storage/app/public/' . $post->video) }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>
        @endif

        {{-- Cảm xúc --}}
        @include('luotthich.reaction', ['post' => $post])
    </div>
    @empty
    <p>Người dùng này chưa có bài viết nào.</p>
    @endforelse
</div>

</div>

{{-- Các overlay và JS --}}
<div id="imageOverlay">
    <button id="prevBtn" class="nav-btn" onclick="prevImage()">⟨</button>
    <img src="" alt="Xem ảnh">
    <button id="nextBtn" class="nav-btn" onclick="nextImage()">⟩</button>
</div>

<div id="commentOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">
        <div id="comments-area">
            <div id="loading-spinner" class="loading-spinner">
                <div class="spinner"></div>
                <p>Đang tải bình luận...</p>
            </div>
        </div>

        {{-- Form bình luận --}}
        @auth
        <form id="commentForm" class="binh_luan" method="POST" action="{{ route('binhluan.store') }}">
            @csrf
            <input type="hidden" name="post_id" id="commentPostId">
            <textarea class="viet_bl" name="noi_dung" rows="1" placeholder="Viết bình luận..."></textarea>
            <button type="submit" class="gui_bl">Gửi</button>
        </form>
        @endauth
    </div>
</div>

@endsection

{{-- JS --}}
<script src="{{ asset('public/js/anh.js') }}"></script>
<script src="{{ asset('public/js/video-autoplay.js') }}"></script>
<script src="{{ asset('public/js/like.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="route-comment-store" content="{{ route('binhluan.store') }}">
<meta name="route-comment-list" content="{{ url('binhluan') }}">
<script src="{{ asset('public/js/binhluan.js') }}"></script>
<script src="{{ asset('public/js/overlay.js') }}"></script>