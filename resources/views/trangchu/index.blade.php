@extends('layouts.app')

@section('title', 'Trang chủ')

<link rel="stylesheet" href="{{ asset('public/css/like.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/binhluan.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/chat.css') }}">
@section('content')
<div class="feed">

    {{-- Nếu đã đăng nhập --}}
    @auth
    {{-- Form đăng bài --}}
    <div class="post-box1">
        <form method="POST" action="{{ route('baiviet.store') }}" enctype="multipart/form-data">
            @csrf
            <textarea name="noi_dung" rows="3" placeholder="Bạn đang nghĩ gì?"></textarea><br>

            <div class="file-inputs">
                <label class="custom-file">
                    <i class="bi bi-images"></i> Ảnh
                    <input type="file" name="hinh_anh[]" accept="image/*" multiple id="hinhAnhInput">
                    <span id="errorMsg" style="color: red; font-size: 12px; display: none;">Lỗi</span>
                    <div id="fileCount" style="font-size: 12px; color: gray; margin-top: 3px;"></div>
                </label>
                <label class="custom-file">
                    <i class="bi bi-youtube"></i> Video
                    <input type="file" name="video" accept="video/*" id="videoInput" multiple>
                    <div id="videoCount" style="font-size: 12px; color: gray; margin-top: 3px;"></div>
                </label>
            </div>

            <button type="submit">Đăng bài</button>
        </form>
    </div>

    @include('story.story', ['stories' => $stories, 'allStories' => $allStories])



    {{-- Bài viết --}}
    @foreach($baiviets as $post)
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

            {{-- Xóa bài viết nếu là chủ bài --}}
            @if(Auth::id() === $post->user->id)
            <div class="xoa_bd">
                <form method="POST" action="{{ route('baiviet.destroy', $post->id) }}"
                    onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn_xoabd" type="submit"><i class="bi bi-trash-fill"></i></button>
                </form>
            </div>
            @endif
        </div>

        <p>{{ $post->noi_dung }}</p>
        @if($post->sanPham)
        <p>
            <a class="linksp" href="{{ route('sanpham.chitiet', $post->sanPham->id) }}" target="_blank">
                <i class="bi bi-link"></i> Đi tới sản phẩm
            </a>
        </p>
        @endif

        {{-- Ảnh --}}
        @if($post->hinh_anh)
        @php
        $images = $post->hinh_anh ?? [];
        $count = count($images);
        $displayCount = $count > 4 ? 4 : $count;
        $wrapperClass = 'image-gallery ';
        if ($count === 1) $wrapperClass .= 'count-1';
        elseif ($count === 2) $wrapperClass .= 'count-2';
        elseif ($count === 3) $wrapperClass .= 'count-3';
        elseif ($count === 4) $wrapperClass .= 'count-4';
        else $wrapperClass .= 'count-5plus';
        @endphp

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



    {{-- Video --}}
    @if($post->video)
    <div class="video-wrapper">
        <video class="auto-play-video" controls muted>
            <source src="{{ asset('storage/app/public/' . $post->video) }}" type="video/mp4">
        </video>
    </div>
    @endif
    {{-- Link sản phẩm nếu có --}}



    @include('luotthich.reaction', ['post' => $post])



</div>
@endforeach

@endauth

{{-- Nếu chưa đăng nhập --}}
@guest
<div class="post-box">Vui lòng đăng nhập để xem bảng tin.</div>
@endguest

</div>
@endsection
<script src="{{ asset('public/js/video-autoplay.js') }}"></script>

<!-- <div id="imageOverlay">
    <button id="prevBtn" class="nav-btn" onclick="prevImage()"><i class="bi bi-caret-left-fill"></i></button>
    <img src="" alt="Xem ảnh">
    <button id="nextBtn" class="nav-btn" onclick="nextImage()"><i class="bi bi-caret-right-fill"></i></button>
</div> -->

<div id="commentOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">

        <div id="comments-area">
            <div id="loading-spinner" class="loading-spinner">
                <div class="spinner"></div>
                <p>Đang tải bình luận...</p>
            </div>
        </div>
        {{-- Form nhập bình luận --}}
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

<script>
    document.addEventListener('click', function(e) {
        // Kiểm tra nếu phần tử được click là ảnh trong .image-item
        if (e.target.matches('.image-item img')) {
            const postBox = e.target.closest('.post-box');
            if (!postBox) return;

            const postId = postBox.dataset.postId;

            // ✅ Chuyển hướng bằng route của Laravel (render sẵn trong Blade)
            const baseUrl = "{{ route('baiviet.chitiet', ':id') }}".replace(':id', postId);
            window.location.href = baseUrl;
        }
    });
</script>

<script>
    document.addEventListener('click', function(e) {
        // Kiểm tra xem người dùng có click vào video không
        if (e.target.matches('.auto-play-video')) {
            const postBox = e.target.closest('.post-box');
            if (!postBox) return;

            const postId = postBox.dataset.postId;

            // ✅ Không chuyển trang nếu người dùng click vào thanh điều khiển video
            const video = e.target;
            const rect = video.getBoundingClientRect();
            const clickInControls = e.clientY > rect.bottom - 45; // vùng controls cao khoảng 45px
            if (clickInControls) return;

            // ✅ Điều hướng đến trang chi tiết video
            window.location.href = "{{ url('baiviet') }}/" + postId;
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hinhAnhInput = document.getElementById('hinhAnhInput');
        const fileCount = document.getElementById('fileCount');
        const errorMsg = document.getElementById('errorMsg');

        if (hinhAnhInput && fileCount) {
            // Hiển thị số ảnh khi thay đổi
            hinhAnhInput.addEventListener('change', function() {
                const files = hinhAnhInput.files;
                if (!files) {
                    fileCount.textContent = '';
                    return;
                }

                // Ví dụ: giới hạn số file (nếu cần)
                const MAX_FILES = 10;
                if (files.length > MAX_FILES) {
                    if (errorMsg) {
                        errorMsg.style.display = 'block';
                        errorMsg.textContent = `Chỉ được chọn tối đa ${MAX_FILES} ảnh`;
                    }
                    // optional: bạn có thể reset input nếu vượt quá
                    // hinhAnhInput.value = '';
                    // fileCount.textContent = '';
                    return;
                } else {
                    if (errorMsg) {
                        errorMsg.style.display = 'none';
                    }
                }

                // Nếu muốn hiển thị tên các file (bỏ comment nếu cần)
                // const names = Array.from(files).map(f => f.name).join(', ');
                // fileCount.textContent = `${files.length} ảnh đã chọn: ${names}`;

                fileCount.textContent = files.length + (files.length > 1 ? ' ảnh đã chọn' : ' ảnh đã chọn');
            });
        }

        const videoInput = document.getElementById('videoInput');
        const videoCount = document.getElementById('videoCount');

        if (videoInput && videoCount) {
            videoInput.addEventListener('change', function() {
                const files = videoInput.files;
                if (!files || files.length === 0) {
                    videoCount.textContent = '';
                    return;
                }
                videoCount.textContent = files.length + (files.length > 1 ? ' video đã chọn' : ' video đã chọn');
            });
        }
    });
</script>

<script src="{{ asset('public/js/story.js') }}"></script>
<script src="{{ asset('public/js/like.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="route-comment-store" content="{{ route('binhluan.store') }}">
<meta name="route-comment-list" content="{{ url('binhluan') }}">
<script src="{{ asset('public/js/binhluan.js') }}"></script>
<script src="{{ asset('public/js/overlay.js') }}"></script>