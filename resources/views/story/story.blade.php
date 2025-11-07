<div class="story-container">

    <div class="story-item" onclick="openStoryDang()">
        <img class="story-background"
            src="{{ Auth::user()->anh_dai_dien ? asset('storage/app/public/' . Auth::user()->anh_dai_dien) : asset('public/uploads/default.png') }}">
        <div class="upload-container">
            <div class="upload-label">
                <div class="upload-button">+</div>
                <div class="phantrang">
                    <label class="tao_tin">Tạo tin</label>
                </div>
            </div>
        </div>
    </div>

    @php
    $myStories = $stories->where('user_id', Auth::id());
    $otherStories = $stories->where('user_id', '!=', Auth::id());
    $sortedStories = $myStories->concat($otherStories);
    @endphp


    @foreach($sortedStories as $story)
    <div class="story-item" onclick="openStory({{ $story->user->id }})">
        @if($story->hinh_anh)
        <img class="story-background" src="{{ asset('storage/app/public/' . $story->hinh_anh) }}">
        @elseif($story->video)
        <video class="story-background" muted autoplay loop>
            <source src="{{ asset('storage/app/public/' . $story->video) }}" type="video/mp4">
        </video>
        @else
        <img class="story-background" src="{{ asset('uploads/default_story.png') }}">
        @endif

        <div class="story-avatar">
            <img src="{{ $story->user->anh_dai_dien 
                ? asset('storage/app/public/' . $story->user->anh_dai_dien) 
                : asset('public/uploads/default.png') }}">
        </div>
        <div class="story-name">{{ $story->user->name ?? $story->user->username }}</div>

        @if($story->user_id === Auth::id())
        <button class="delete-story-btn" onclick="event.stopPropagation(); deleteStory({{ $story->id }})">
            <i class="bi bi-trash3"></i>
        </button>
        @endif
    </div>
    @endforeach
</div>


<div id="overlaystorydang" class="overlaystorydang" onclick="if(event.target.id === 'overlaystorydang') closeOverlay()">
    <div class="overlaystorychon" onclick="event.stopPropagation()">
        <h3>Chọn loại nội dung</h3>
        <button class="btndangstroryanh" onclick="selectImage()">Đăng ảnh <i class="bi bi-card-image"></i></button>
        <button class="btndangstroryvideo" onclick="selectVideo()">Đăng video <i class="bi bi-camera-reels-fill"></i></button>
        <button class="btnhuydangstrory" onclick="closeOverlay()" style="margin-top: 10px;">Hủy</button>


        <form id="storyForm" method="POST" action="{{ route('story.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" id="storyImage" name="hinh_anh" accept="image/*" hidden onchange="submitStory()">
            <input type="file" id="storyVideo" name="video" accept="video/*" hidden onchange="submitStory()">
        </form>
    </div>
</div>

<div id="storyOverlay" class="story-overlay">
    <div class="story-content">
        <button class="story-btn prev" onclick="prevStory()">⬅</button>
        <img id="storyImageViewer" class="story-media">
        <video id="storyVideoViewer" class="story-media" controls></video>
        <button class="story-btn next" onclick="nextStory()">➡</button>
    </div>
</div>


<script>
    window.storyUserOrder = <?php echo json_encode($sortedStories->pluck('user_id')->unique()->values()); ?>;
    let allStories = <?php echo json_encode($allStories); ?>;
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        let allStories = @json($allStories);
        let currentStories = [];
        let storyIndex = 0;
        let currentUserId = null;

        // 👉 Mở story theo user
        window.openStory = function(userId) {
            currentUserId = userId;
            currentStories = allStories.filter(s => s.user_id == userId);
            storyIndex = 0;
            showStory(storyIndex);
            document.getElementById("storyOverlay").style.display = "flex";
        }

        // 👉 Hiển thị story
        window.showStory = function(index) {
            const story = currentStories[index];
            const img = document.getElementById("storyImageViewer");
            const video = document.getElementById("storyVideoViewer");

            video.pause();
            video.src = "";
            video.load();

            img.style.display = "none";
            video.style.display = "none";

            if (story.hinh_anh) {
                img.src = "storage/app/public/" + story.hinh_anh;
                img.style.display = "block";
            } else if (story.video) {
                video.src = "storage/app/public/" + story.video;
                video.style.display = "block";
                video.play().catch(() => {});
            }
        }

        // 👉 Sang story kế
        window.nextStory = function() {
            const allUserIds = window.storyUserOrder;

            if (storyIndex < currentStories.length - 1) {
                storyIndex++;
                showStory(storyIndex);
            } else {
                const currentPos = allUserIds.indexOf(currentUserId);
                const nextPos = currentPos + 1;

                if (nextPos < allUserIds.length) {
                    const nextUserId = allUserIds[nextPos];
                    openStory(nextUserId);
                } else {
                    closeStory();
                }
            }
        }

        // 👉 Lùi story trước
        window.prevStory = function() {
            const allUserIds = window.storyUserOrder;

            if (storyIndex > 0) {
                storyIndex--;
                showStory(storyIndex);
            } else {
                const currentPos = allUserIds.indexOf(currentUserId);
                const prevPos = currentPos - 1;

                if (prevPos >= 0) {
                    const prevUserId = allUserIds[prevPos];
                    openStory(prevUserId);
                    currentStories = allStories.filter(s => s.user_id == prevUserId);
                    storyIndex = currentStories.length - 1;
                    showStory(storyIndex);
                } else {
                    closeStory();
                }
            }
        }

        // 👉 Đóng story
        window.closeStory = function() {
            const overlay = document.getElementById("storyOverlay");
            const video = document.getElementById("storyVideoViewer");

            video.pause();
            video.src = "";
            video.load();

            overlay.style.display = "none";
        }

        // Đóng khi click nền
        const overlay = document.getElementById("storyOverlay");
        overlay.addEventListener("click", function(e) {
            if (e.target.id === "storyOverlay") closeStory();
        });

        // Chặn click bên trong nội dung story
        const content = document.querySelector(".story-content");
        if (content) {
            content.addEventListener("click", e => e.stopPropagation());
        }
    });
</script>
<script>
    function deleteStory(storyId) {
        if (!confirm('Bạn có chắc muốn xoá story này?')) return;

        fetch(`{{ url('/story') }}/${storyId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Đã xoá story thành công!');
                    location.reload(); // hoặc xóa phần tử khỏi DOM nếu muốn
                } else {
                    alert('Không thể xoá story.');
                }
            })
            .catch(err => console.error(err));
    }
</script>