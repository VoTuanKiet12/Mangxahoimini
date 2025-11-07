document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('imageOverlay');
    const overlayImg = overlay.querySelector('img');
    const imageItems = document.querySelectorAll('.image-gallerybvct .image-item img');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentIndex = 0;
    let imageUrls = [];

    // Lưu danh sách ảnh
    imageItems.forEach(img => imageUrls.push(img.src));

    // Khi click vào ảnh => mở overlay
    imageItems.forEach((img, index) => {
        img.addEventListener('click', () => {
            currentIndex = index;
            overlayImg.src = imageUrls[currentIndex];
            overlay.style.display = 'flex';
        });
    });

    // Nút Prev
    window.prevImage = function () {
        currentIndex = (currentIndex - 1 + imageUrls.length) % imageUrls.length;
        overlayImg.src = imageUrls[currentIndex];
    };

    // Nút Next
    window.nextImage = function () {
        currentIndex = (currentIndex + 1) % imageUrls.length;
        overlayImg.src = imageUrls[currentIndex];
    };

    // Click ra ngoài ảnh => đóng overlay
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            overlay.style.display = 'none';
        }
    });

    // Ấn ESC để đóng
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') overlay.style.display = 'none';
    });
});