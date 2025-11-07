{{-- Bài viết gốc --}}
<div class="post-box-bv">
    <div class="post-header">
        <img src="{{ $post->user->anh_dai_dien 
            ? asset('storage/app/public/' . $post->user->anh_dai_dien) 
            : asset('public/uploads/default.png') }}"
            class="avatar" style="width:40px; height:40px; border-radius:50%;">
        <strong class="name-bv">{{ $post->user->name ?? $post->user->username }}</strong>
        <span class="n-bv">
            {{ \Carbon\Carbon::parse($post->ngay_dang)->diffForHumans() }}
        </span>
    </div>
    <p class="nd-bv">{{ $post->noi_dung }}</p>

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
            <img src="{{ asset('storage/app/public/' . $images[$i]) }}" alt="Ảnh bài viết" onclick="openOverlay({{ $i }})">
            @if($i === 3 && $count > 4)
            <div class="more-overlay">+{{ $count - 4 }}</div>
            @endif
    </div>
    @endfor
</div>
@endif


@if($post->video)
<div style="margin-top:10px;">
    <video controls style="max-width:100%; border-radius:6px;">
        <source src="{{ asset('storage/app/public/'.$post->video) }}" type="video/mp4">
    </video>
</div>
@endif
</div>
<div id="imageOverlay">
    <button id="prevBtn" class="nav-btn" onclick="prevImage()"><i class="bi bi-caret-left-fill"></i></button>
    <img src="" alt="Xem ảnh">
    <button id="nextBtn" class="nav-btn" onclick="nextImage()"><i class="bi bi-caret-right-fill"></i></button>
</div>

{{-- Danh sách bình luận --}}
@if($comments->count() > 0)
@foreach($comments as $cmt)
@include('comments.item', ['cmt' => $cmt])
@endforeach
@else
<p>Chưa có bình luận nào.</p>
@endif