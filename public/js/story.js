function openStoryDang() {
    document.getElementById("overlaystorydang").style.display = "flex";
}

// 👉 Chọn đăng ảnh
function selectImage() {
    document.getElementById('storyImage').click();
}

// 👉 Chọn đăng video
function selectVideo() {
    document.getElementById('storyVideo').click();
}

// 👉 Gửi form khi chọn file
function submitStory() {
    document.getElementById('storyForm').submit();
    closeOverlay();
}

// 👉 Đóng overlay
function closeOverlay() {
    const overlay = document.getElementById("overlaystorydang");
    if (overlay) overlay.style.display = "none";
}