document.addEventListener("DOMContentLoaded", () => {
    const gallery = document.getElementById("imageGallery");
    if (!gallery) return;

    const images = JSON.parse(gallery.dataset.images);
    const baseUrl = window.baseStorageUrl; // ✅ Lấy từ Blade
    let currentIndex = 0;

    function openOverlay(index) {
        currentIndex = index;
        const overlay = document.getElementById("imageOverlay");
        const img = document.getElementById("overlayImage");
        img.src = baseUrl + images[currentIndex]; // ✅ Ghép đúng đường dẫn thật
        overlay.style.display = "flex";
    }

    function closeOverlay() {
        document.getElementById("imageOverlay").style.display = "none";
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        document.getElementById("overlayImage").src = baseUrl + images[currentIndex];
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        document.getElementById("overlayImage").src = baseUrl + images[currentIndex];
    }

    // ✅ Đóng khi click ra ngoài
    const overlay = document.getElementById("imageOverlay");
    if (overlay) {
        overlay.addEventListener("click", (e) => {
            if (e.target.id === "imageOverlay") closeOverlay();
        });
    }

    // ✅ Gán global để gọi từ HTML onclick
    window.openOverlay = openOverlay;
    window.prevImage = prevImage;
    window.nextImage = nextImage;
});

