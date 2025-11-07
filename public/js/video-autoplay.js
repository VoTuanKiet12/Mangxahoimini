document.addEventListener("DOMContentLoaded", function () {
    const videos = document.querySelectorAll(".auto-play-video");

    // Auto play/pause khi lướt
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.play();
            } else {
                entry.target.pause();
            }
        });
    }, {
        threshold: 0.5
    });

    videos.forEach(video => {
        observer.observe(video);

        // Khi user bật âm thanh video này -> đồng bộ tất cả video
        video.addEventListener("volumechange", function () {
            if (!video.muted) {
                videos.forEach(v => v.muted = false);
            } else {
                // nếu bạn muốn khi tắt âm 1 video thì tất cả cùng tắt:
                videos.forEach(v => v.muted = true);
            }
        });
    });
});