document.addEventListener("DOMContentLoaded", () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const routeStore = document.querySelector('meta[name="route-comment-store"]').content;
    const routeDestroy = document.querySelector('meta[name="route-comment-destroy"]').content;

    const form = document.getElementById("formBinhLuan");
    const inputAnh = document.getElementById("chonAnhBinhLuan");
    const previewBox = document.getElementById("previewAnhBinhLuan");
    const previewImg = previewBox.querySelector("img");
    const btnXoaAnh = document.getElementById("xoaAnhBinhLuan");

    // 🔹 Xem trước ảnh
    inputAnh.addEventListener("change", e => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e2 => {
                previewImg.src = e2.target.result;
                previewBox.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    });

    btnXoaAnh.addEventListener("click", () => {
        inputAnh.value = "";
        previewBox.style.display = "none";
    });

    // 🔹 Gửi bình luận
    form.addEventListener("submit", async e => {
        e.preventDefault();

        const formData = new FormData(form);
        try {
            const res = await fetch(routeStore, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                document.getElementById("noiDungBinhLuan").value = "";
                inputAnh.value = "";
                previewBox.style.display = "none";

                document.querySelector(".ds-binh-luan").insertAdjacentHTML("afterbegin", data.html);
            } else {
                alert(data.message || "Có lỗi xảy ra khi gửi bình luận.");
            }

        } catch (err) {
            console.error("Lỗi gửi bình luận:", err);
        }
    });

    // 🔹 Xóa bình luận
    document.addEventListener('click', async (e) => {
        if (e.target.classList.contains('delete-comment-btn')) {
            const id = e.target.dataset.id;
            if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;

            try {
                const res = await fetch(`${routeDestroy}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    // Xóa phần tử khỏi giao diện
                    const item = document.getElementById(`comment-${id}`);
                    if (item) item.remove();
                } else {
                    alert(data.message || 'Không thể xóa bình luận.');
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi khi xóa bình luận.');
            }
        }
    });
});
