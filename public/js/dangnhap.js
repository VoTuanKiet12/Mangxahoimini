// Animations: Chuyển panel đăng ký / đăng nhập
const registerButton = document.getElementById("register");
const loginButton = document.getElementById("login");
const container = document.getElementById("container1");

registerButton.addEventListener("click", () => {
    container.classList.add("right-panel-active");
});

loginButton.addEventListener("click", () => {
    container.classList.remove("right-panel-active");
});
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form'); // hoặc form cụ thể nếu có id
    const matKhau = document.getElementById('MatKhau');
    const xacNhanMatKhau = document.getElementById('XacNhanMatKhau');
    const errorMsg = document.getElementById('XacNhanMatKhau-error');

    form.addEventListener('submit', function (e) {
        if (matKhau.value !== xacNhanMatKhau.value) {
            e.preventDefault(); // Ngăn form gửi đi
            errorMsg.textContent = 'Mật khẩu xác nhận không khớp!';
            errorMsg.style.color = 'red';
            xacNhanMatKhau.focus();
        } else {
            errorMsg.textContent = ''; // Xóa lỗi nếu đúng
        }
    });
});
