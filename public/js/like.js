document.addEventListener('DOMContentLoaded', function () {
    const reactionBoxes = document.querySelectorAll('.reaction-box');

    reactionBoxes.forEach(box => {
        let hideTimeout;

        const options = box.querySelector('.reaction-options');

        // Hover vào nút => hiện menu
        box.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);
            box.classList.add('active');
        });

        // Rời khỏi => ẩn sau 300ms (để kịp rê chuột xuống)
        box.addEventListener('mouseleave', () => {
            hideTimeout = setTimeout(() => {
                box.classList.remove('active');
            }, 300);
        });

        // Nếu rê chuột vào menu => không tắt
        options.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);
        });

        // Rời khỏi menu => đóng
        options.addEventListener('mouseleave', () => {
            hideTimeout = setTimeout(() => {
                box.classList.remove('active');
            }, 300);
        });
    });
});