@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/like.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/binhluan.css') }}">
@section('title', 'Tất cả video bài viết')

@section('content')
<div class="container mt-4">
    @forelse($dsVideo as $post)
    <div class="post-box" data-post-id="{{ $post->id }}">

        <div class="post-header">
            <a href="{{ route('user.show', $post->user->id) }}"
                style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">

                <img src="{{ $post->user->anh_dai_dien
                        ? asset('storage/app/public/' . $post->user->anh_dai_dien)
                        : asset('public/uploads/default.png') }}"
                    class="avatar-bv"
                    alt="{{ $post->user->name ?? $post->user->username }}">

                <strong>{{ $post->user->name ?? $post->user->username }}</strong>
            </a>

            <span class="ngay-tao">{{ \Carbon\Carbon::parse($post->ngay_dang)->format('d/m/Y') }}</span>


            @if(Auth::id() === $post->user->id)
            <div class="xoa_bd">
                <form method="POST"
                    action="{{ route('baiviet.destroy', $post->id) }}"
                    onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?')"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn_xoabd" type="submit"><i class="fas fa-trash-alt"></i></button>
                </form>
            </div>
            @endif
        </div>


        @if($post->noi_dung)
        <p class="noi-dung">{{ $post->noi_dung }}</p>
        @endif
        @if($post->sanPham)
        <p>
            <a class="linksp" href="{{ route('sanpham.chitiet', $post->sanPham->id) }}" target="_blank">
                <i class="bi bi-link"></i> Đi tới sản phẩm
            </a>
        </p>
        @endif

        @if($post->video)
        <div class="video-wrapper">
            <video class="auto-play-video" controls muted preload="metadata" loop>
                <source src="{{ asset('storage/app/public/' . $post->video) }}" type="video/mp4">
                Trình duyệt của bạn không hỗ trợ video.
            </video>
        </div>
        @endif


        @include('luotthich.reaction', ['post' => $post])
    </div>
    @empty
    <p class="text-muted">Hiện chưa có bài viết nào có video.</p>
    @endforelse
</div>


<div id="commentOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">

        <div id="comments-area">
            <div id="loading-spinner" class="loading-spinner">
                <div class="spinner"></div>
                <p>Đang tải bình luận...</p>
            </div>
        </div>

        @auth
        <form id="commentForm" class="binh_luan" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="post_id" id="commentPostId">
            <div id="previewImagebl" class="preview-box" style="display:none;">
                <img src="" alt="Ảnh bình luận" class="preview-img">
                <button type="button" id="removePreviewbl" title="Xóa ảnh">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
            <div class="comment-input-area">
                <label for="commentImage" class="add-image-btn" title="Thêm ảnh">
                    <i class="bi bi-image"></i>
                </label>
                <input type="file" id="commentImage" name="hinh_anh" accept="image/*" style="display:none;">
                <textarea class="viet_bl" name="noi_dung" rows="1" placeholder="Viết bình luận..."></textarea>
                <button type="submit" class="gui_bl"><i class="bi bi-send"></i></button>
            </div>
        </form>

        @endauth

    </div>
</div>
@endsection
<script src="{{ asset('public/js/video-autoplay.js') }}"></script>
<script src="{{ asset('public/js/like.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="route-comment-store" content="{{ route('binhluan.store') }}">
<meta name="route-comment-list" content="{{ url('binhluan') }}">
<script src="{{ asset('public/js/binhluan.js') }}"></script>