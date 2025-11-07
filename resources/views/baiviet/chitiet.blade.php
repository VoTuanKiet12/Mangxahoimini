@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/like.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('public/css/chitietbaiviet.css') }}">

@section('content')
<div class="page-wrapper">
    <div class="post-detail-wrapper">

        {{-- 🖼️ CỘT TRÁI: Bài viết --}}
        <div class="post-left">
            <div class="post-box" data-post-id="{{ $baiViet->id }}">

                {{-- Header --}}
                <div class="post-header">
                    <a href="{{ route('user.show', $baiViet->user->id) }}"
                        style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;">
                        <img src="{{ $baiViet->user->anh_dai_dien 
                            ? asset('storage/app/public/' . $baiViet->user->anh_dai_dien) 
                            : asset('public/uploads/default.png') }}"
                            class="avatar-bv">
                        <strong>{{ $baiViet->user->name ?? $baiViet->user->username }}</strong>
                    </a>
                    <span class="ngay-tao">{{ \Carbon\Carbon::parse($baiViet->ngay_dang)->format('d/m/Y H:i') }}</span>
                </div>

                {{-- Nội dung bài viết --}}
                @if($baiViet->noi_dung)
                <p class="noi-dung-bv">{{ $baiViet->noi_dung }}</p>
                @endif

                {{-- Ảnh --}}
                @if(!empty($baiViet->hinh_anh) && is_array($baiViet->hinh_anh))
                @php
                $images = $baiViet->hinh_anh;
                $count = count($images);
                $displayCount = $count > 4 ? 4 : $count;
                $wrapperClass = 'image-gallerybvct ';
                if ($count === 1) $wrapperClass .= 'count-1';
                elseif ($count === 2) $wrapperClass .= 'count-2';
                elseif ($count === 3) $wrapperClass .= 'count-3';
                elseif ($count === 4) $wrapperClass .= 'count-4';
                else $wrapperClass .= 'count-5plus';
                @endphp

                <div class="{{ $wrapperClass }}">
                    @foreach($images as $index => $img)
                    @if($index < $displayCount)
                        <div class="image-item" onclick="openOverlay({{ $index }})" style="position:relative;">
                        <img src="{{ asset('storage/app/public/' . $img) }}" alt="Ảnh bài viết">
                        @if($index === 3 && $count > 4)
                        <div class="overlay" onclick="openOverlay({{ $index }})">+{{ $count - 4 }}</div>
                        @endif
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- Video --}}
            @if($baiViet->video)
            <div class="video-wrapper">
                <video class="auto-play-video" controls>
                    <source src="{{ asset('storage/app/public/' . $baiViet->video) }}" type="video/mp4">
                </video>
            </div>
            @endif

            {{-- Reaction --}}
            @include('luotthich.reaction', ['post' => $baiViet])
        </div>
    </div>

    {{-- 💬 CỘT PHẢI: Bình luận --}}
    <div class="post-right">
        <div class="comments-section">
            <h4>Bình luận</h4>
            <div class="overlay-content">
                <div id="comments-area" class="ds-binh-luan">
                    @if($baiViet->binhLuan->count() > 0)
                    @foreach($baiViet->binhLuan->sortByDesc('ngay_binh_luan') as $cmt)
                    @include('comments.item', ['cmt' => $cmt])
                    @endforeach
                    @else
                    <p>Chưa có bình luận nào.</p>
                    @endif
                </div>

                @auth
                <form id="formBinhLuan" class="form-binh-luan" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                    @csrf
                    <input type="hidden" name="post_id" id="postIdBinhLuan" value="{{ $baiViet->id }}">

                    {{-- 🔹 Xem trước ảnh bình luận --}}
                    <div id="previewAnhBinhLuan" class="preview-box" style="display:none;">
                        <img src="" alt="Ảnh bình luận" class="preview-img">
                        <button type="button" id="xoaAnhBinhLuan" title="Xóa ảnh">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>

                    {{-- 🔹 Ô nhập bình luận --}}
                    <div class="comment-input-area">
                        <label for="chonAnhBinhLuan" class="add-image-btn" title="Thêm ảnh">
                            <i class="bi bi-image"></i>
                        </label>
                        <input type="file" id="chonAnhBinhLuan" name="hinh_anh" accept="image/*" style="display:none;">
                        <textarea id="noiDungBinhLuan" name="noi_dung" rows="1" placeholder="Viết bình luận..."></textarea>
                        <button type="submit" class="gui_bl" id="guiBinhLuan"><i class="bi bi-send"></i></button>
                    </div>
                </form>

                @endauth
            </div>
        </div>
    </div>

</div>
</div>
@endsection

{{-- 🔧 JS và overlay nên để ngoài section --}}
<div id="imageOverlay" style="display:none;">
    <button id="prevBtn" class="nav-btn" onclick="prevImage()"><i class="bi bi-caret-left-fill"></i></button>
    <img id="overlayImage" src="" alt="Xem ảnh">
    <button id="nextBtn" class="nav-btn" onclick="nextImage()"><i class="bi bi-caret-right-fill"></i></button>
</div>
<script>
    window.baseStorageUrl = "{{ asset('public/storage') }}/";
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="route-comment-store" content="{{ route('binhluan.store') }}">
<meta name="route-comment-list" content="{{ url('binhluan') }}">
<meta name="route-comment-destroy" content="{{ url('binhluan') }}">

<div id="imageGallery" data-images='@json($baiViet->hinh_anh)'></div>



<script src="{{ asset('public/js/binhluanchitiet.js') }}"></script>
<script src="{{ asset('public/js/anhctbv.js') }}"></script>