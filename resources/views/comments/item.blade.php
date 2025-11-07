<div class="comment-item" id="comment-{{ $cmt->id }}">
    <img class="comment-avatar"
        src="{{ $cmt->user->anh_dai_dien 
                ? asset('storage/app/public/' . $cmt->user->anh_dai_dien) 
                : asset('public/uploads/default.png') }}"
        alt="avatar">

    <div class="comment-content">
        <div class="namebl">
            <strong>{{ $cmt->user->name ?? $cmt->user->username ?? 'Người dùng' }}</strong>

            {{-- Nút xóa: chỉ hiện nếu là chủ bình luận hoặc admin --}}
            @if(auth()->check() && (auth()->id() === $cmt->user_id || auth()->user()->role === 'admin'))
            <button type="button" class="delete-comment-btn" data-id="{{ $cmt->id }}">Xóa</button>
            @endif
        </div>

        {{-- Nội dung --}}
        @if($cmt->noi_dung)
        <p>{{ $cmt->noi_dung }}</p>
        @endif

        {{-- Ảnh bình luận --}}
        @if($cmt->hinh_anh)
        <div class="comment-image">
            <img class="imagebl" src="{{ asset('storage/app/public/' . $cmt->hinh_anh) }}" alt="Ảnh bình luận" style="cursor:pointer;">
        </div>
        @endif

        <small>{{ \Carbon\Carbon::parse($cmt->ngay_binh_luan)->diffForHumans() }}</small>
    </div>
</div>
<!-- Overlay xem ảnh bình luận -->
<div id="imageOverlaybl" class="image-overlaybl">
    <img id="overlayImagebl" class="overlay-imagebl" src="" alt="Ảnh bình luận">
</div>