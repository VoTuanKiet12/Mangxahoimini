@php
$tatCaCamXuc = $post->luotThich ?? collect();
$camXucTheoLoai = $tatCaCamXuc->groupBy('cam_xuc');
$camXucCuaToi = $tatCaCamXuc->where('user_id', auth()->id())->first()->cam_xuc ?? null;

// 🌟 Dùng ảnh thay vì emoji
$dsCamXuc = [
'like' => [
'icon' => '<img src="'.asset('public/uploads/icons/like.png').'" alt="Thích" class="icon-cx">',
'ten' => 'Thích'
],
'love' => [
'icon' => '<img src="'.asset('public/uploads/icons/love.png').'" alt="Yêu thích" class="icon-cx">',
'ten' => 'Yêu thích'
],
'haha' => [
'icon' => '<img src="'.asset('public/uploads/icons/haha.png').'" alt="Haha" class="icon-cx">',
'ten' => 'Haha'
],
'wow' => [
'icon' => '<img src="'.asset('public/uploads/icons/wow.png').'" alt="Wow" class="icon-cx">',
'ten' => 'Wow'
],
'sad' => [
'icon' => '<img src="'.asset('public/uploads/icons/sad.png').'" alt="Buồn" class="icon-cx">',
'ten' => 'Buồn'
],
'angry' => [
'icon' => '<img src="'.asset('public/uploads/icons/angry.png').'" alt="Phẫn nộ" class="icon-cx">',
'ten' => 'Phẫn nộ'
],
];
$tongCamXuc = $tatCaCamXuc->count();
$demCamXuc = [];
foreach ($camXucTheoLoai as $loai => $ds) {
$demCamXuc[$loai] = $ds->count();
}
arsort($demCamXuc);
$top3CamXuc = array_slice($demCamXuc, 0, 3, true);
@endphp


@if ($tongCamXuc >= 0)
<div class="reaction-summary">
    @foreach ($top3CamXuc as $loai => $count)
    @if (isset($dsCamXuc[$loai]))
    <span class="reaction-icon"> {!! $dsCamXuc[$loai]['icon'] !!} </span>
    @endif
    @endforeach
    <span class="reaction-count"> {{ $tongCamXuc }}</span>
</div>
@endif

<div class="post-actions">
    <div class="reaction-box">

        <button type="button" class="like-btn">
            @if ($camXucCuaToi)
            {!! $dsCamXuc[$camXucCuaToi]['icon'] !!} {{ $dsCamXuc[$camXucCuaToi]['ten'] }}
            @else
            <i class="bi bi-hand-thumbs-up-fill"></i> Thích
            @endif
        </button>


        <div class="reaction-options">
            @foreach ($dsCamXuc as $loai => $data)
            <form method="POST" action="{{ route('like', $post->id) }}" class="reaction-form ajax-reaction">
                @csrf
                <input type="hidden" name="cam_xuc" value="{{ $loai }}">
                <button type="submit" class="reaction-btn" title="{{ $data['ten'] }}">
                    {!! $data['icon'] !!}
                </button>
            </form>
            @endforeach
            <button type="button" class="reaction-btn remove-react" title="Bỏ cảm xúc">
                <img src="{{ asset('public/uploads/icons/remove.png') }}" alt="Bỏ cảm xúc" class="icon-cx">
            </button>
        </div>
    </div>

    <button type="button" class="comment-btn" data-post-id="{{ $post->id }}">
        <i class="fa-regular fa-comment-dots"></i> Bình luận
    </button>

    <button type="button" class="share-btn">
        <i class="fa-solid fa-share"></i> Chia sẻ
    </button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const reactionForms = document.querySelectorAll(".ajax-reaction");

        reactionForms.forEach(form => {
            form.addEventListener("submit", async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const url = form.action;
                const postBox = form.closest(".post-box");
                const likeBtn = postBox.querySelector(".like-btn");
                const reactionCount = postBox.querySelector(".reaction-count");
                const reactionSummary = postBox.querySelector(".reaction-summary");

                try {
                    const response = await fetch(url, {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": form.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        const cx = result.cam_xuc;
                        const trangThai = result.trang_thai;

                        // Danh sách icon cảm xúc
                        const icons = {
                            like: "<img src='{{ asset('public/uploads/icons/like.png') }}' alt='Thích' class='icon-cx'>",
                            love: "<img src='{{ asset('public/uploads/icons/love.png') }}' alt='Yêu thích' class='icon-cx'>",
                            haha: "<img src='{{ asset('public/uploads/icons/haha.png') }}' alt='Haha' class='icon-cx'>",
                            wow: "<img src='{{ asset('public/uploads/icons/wow.png') }}' alt='Wow' class='icon-cx'>",
                            sad: "<img src='{{ asset('public/uploads/icons/sad.png') }}' alt='Buồn' class='icon-cx'>",
                            angry: "<img src='{{ asset('public/uploads/icons/angry.png') }}' alt='Phẫn nộ' class='icon-cx'>"
                        };

                        // ✅ Nếu cảm xúc thay đổi hoặc thêm mới → cập nhật nút
                        if (trangThai !== 'nochange') {
                            likeBtn.innerHTML = icons[cx] + " " + cx.charAt(0).toUpperCase() + cx.slice(1);
                            likeBtn.classList.add("animate-react");
                            setTimeout(() => likeBtn.classList.remove("animate-react"), 400);
                        }

                        // ✅ Cập nhật tổng số cảm xúc
                        if (reactionCount) {
                            reactionCount.textContent = result.tong ?? 0;
                        }

                        // ✅ Cập nhật icon top 3 cảm xúc
                        if (reactionSummary && result.top3) {
                            let newHTML = "";
                            Object.keys(result.top3).forEach(loai => {
                                if (icons[loai]) {
                                    newHTML += `<span class="reaction-icon">${icons[loai]}</span>`;
                                }
                            });
                            newHTML += `<span class="reaction-count">${result.tong}</span>`;
                            reactionSummary.innerHTML = newHTML;
                        }
                    }
                } catch (err) {
                    console.error("🔥 Lỗi khi gửi cảm xúc:", err);
                }
            });
        });

    });
    // =================== BỎ CẢM XÚC ===================
    // =================== BỎ CẢM XÚC ===================
    document.querySelectorAll(".remove-react").forEach(btn => {
        btn.addEventListener("click", async () => {
            const postBox = btn.closest(".post-box");
            const postId = postBox.dataset.postId;
            const likeBtn = postBox.querySelector(".like-btn");
            const reactionCount = postBox.querySelector(".reaction-count");
            const reactionSummary = postBox.querySelector(".reaction-summary");

            const baseUrl = "{{ url('/') }}"; // ✅ Base URL của project
            const icons = {
                like: "<img src='{{ asset('public/uploads/icons/like.png') }}' alt='Thích' class='icon-cx'>",
                love: "<img src='{{ asset('public/uploads/icons/love.png') }}' alt='Yêu thích' class='icon-cx'>",
                haha: "<img src='{{ asset('public/uploads/icons/haha.png') }}' alt='Haha' class='icon-cx'>",
                wow: "<img src='{{ asset('public/uploads/icons/wow.png') }}' alt='Wow' class='icon-cx'>",
                sad: "<img src='{{ asset('public/uploads/icons/sad.png') }}' alt='Buồn' class='icon-cx'>",
                angry: "<img src='{{ asset('public/uploads/icons/angry.png') }}' alt='Phẫn nộ' class='icon-cx'>"
            };
            try {
                const response = await fetch(`${baseUrl}/baiviet/${postId}/unlike`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                if (!response.ok) throw new Error(`Lỗi ${response.status}`);

                const result = await response.json();

                if (result.success) {
                    // Cập nhật nút like về mặc định
                    likeBtn.innerHTML = `<i class="bi bi-hand-thumbs-up-fill"></i> Thích`;

                    // Cập nhật tổng cảm xúc
                    if (reactionCount) reactionCount.textContent = result.tong ?? 0;

                    // Cập nhật top 3
                    if (reactionSummary) {
                        let newHTML = "";
                        if (result.top3 && Object.keys(result.top3).length > 0) {
                            Object.keys(result.top3).forEach(loai => {
                                if (icons[loai]) {
                                    newHTML += `<span class="reaction-icon">${icons[loai]}</span>`;
                                }
                            });
                            newHTML += `<span class="reaction-count">${result.tong}</span>`;
                        } else {
                            newHTML = `<span class="reaction-count">0</span>`;
                        }
                        reactionSummary.innerHTML = newHTML;
                    }
                }
            } catch (err) {
                console.error("🔥 Lỗi khi bỏ cảm xúc:", err);
            }
        });
    });
</script>