document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 🖼️ IMAGE OVERLAY
    // ==========================
    const imageOverlay = document.getElementById("imageOverlay");
    if (imageOverlay) {
        imageOverlay.addEventListener("click", function (e) {
            // Chỉ đóng khi click ra vùng nền (không phải ảnh hoặc nút)
            if (e.target === imageOverlay) {
                imageOverlay.style.display = "none";
            }
        });
    }

    // ==========================
    // 💬 COMMENT OVERLAY
    // ==========================
    const commentOverlay = document.getElementById("commentOverlay");
    if (commentOverlay) {
        commentOverlay.addEventListener("click", function (e) {
            if (e.target === commentOverlay) {
                commentOverlay.style.display = "none";
                pauseAllVideos(); // 🔇 Dừng tất cả video đang phát
            }
        });
    }

    // ==========================
    // ⏸️ HÀM DỪNG TẤT CẢ VIDEO
    // ==========================
    function pauseAllVideos() {
        const videos = document.querySelectorAll("video");
        videos.forEach(video => {
            if (!video.paused) {
                video.pause();
            }
        });
    }

    // ==========================
    // 📖 STORY OVERLAY (overlay1)
    // ==========================
    const storyOverlay = document.getElementById("overlay1");
    if (storyOverlay) {
        storyOverlay.addEventListener("click", function (e) {
            if (e.target === storyOverlay) {
                storyOverlay.style.display = "none";
            }
        });
    }

    // ==========================
    // 📝 KIỂM TRA FORM TRƯỚC KHI ĐĂNG BÀI
    // ==========================
    const form = document.querySelector('.post-box1 form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const content = form.querySelector('textarea[name="noi_dung"]').value.trim();
            const images = form.querySelector('input[name="hinh_anh[]"]').files.length;
            const video = form.querySelector('input[name="video"]').files.length;

            if (content === "" && images === 0 && video === 0) {
                e.preventDefault();
                alert("Vui lòng nhập nội dung hoặc chọn ảnh/video trước khi đăng bài!");
            }
        });
    }
});
