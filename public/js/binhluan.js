document.addEventListener("DOMContentLoaded", function () {
    // === LẤY ROUTE VÀ TOKEN TỪ META TAG ===
    const routes = {
        commentStore: document.querySelector('meta[name="route-comment-store"]')?.content || '',
        commentList: document.querySelector('meta[name="route-comment-list"]')?.content || '',
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // === SỰ KIỆN CLICK NÚT BÌNH LUẬN ===
    const commentBtns = document.querySelectorAll(".comment-btn");
    commentBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const postBox = btn.closest(".post-box");
            const video = postBox?.querySelector("video");
            if (video && !video.paused) video.pause();

            const postId = btn.dataset.postId;
            if (!postId) {
                console.error("❌ Không tìm thấy postId trong nút bình luận!");
                return;
            }

            openCommentOverlay(postId);
        });
    });

    // === GỬI FORM BÌNH LUẬN (CÓ ẢNH) ===
    const commentForm = document.getElementById("commentForm");
    const inputFile = document.getElementById("commentImage");
    const previewBox = document.getElementById("previewImagebl");
    const previewImg = previewBox ? previewBox.querySelector(".preview-img") : null;
    const removeBtn = document.getElementById("removePreviewbl");

    if (commentForm) {
        commentForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(commentForm);
            const postId = document.getElementById("commentPostId").value;
            const submitBtn = commentForm.querySelector('button[type="submit"]');

            submitBtn.disabled = true;
            showSpinner();

            fetch(routes.commentStore, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then(async (res) => {
                    if (!res.ok) {
                        const text = await res.text();
                        console.error("Server error:", text);
                        throw new Error("Server returned " + res.status);
                    }
                    return res.json();
                })
                .then((data) => {
                    submitBtn.disabled = false;
                    hideSpinner();

                    if (data.success) {
                        // 🟢 Thành công
                        commentForm.reset();
                        if (previewBox) {
                            previewBox.style.display = "none";
                            previewImg.src = "";
                        }
                        loadComments(postId);
                    } else {
                        // 🔴 Bình luận trống hoặc lỗi khác
                        hideSpinner();

                        // 🟠 Hiển thị cảnh báo
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: "warning",
                                title: "Cảnh báo",
                                text: data.message || "Bình luận không hợp lệ!",
                                timer: 1800,
                                showConfirmButton: false
                            });
                        } else {
                            alert("⚠️ " + (data.message || "Bình luận không hợp lệ!"));
                        }

                        // 🌀 Load lại overlay để cập nhật dữ liệu mới nhất
                        loadComments(postId);
                    }
                })
                .catch((err) => {
                    submitBtn.disabled = false;
                    hideSpinner();
                    console.error("⚠️ Lỗi khi gửi bình luận:", err);
                });
        });
    }

    // === HIỂN THỊ ẢNH PREVIEW ===
    if (inputFile && previewBox && previewImg && removeBtn) {
        inputFile.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImg.src = event.target.result;
                    previewBox.style.display = "inline-block";
                };
                reader.readAsDataURL(file);
            }
        });

        removeBtn.addEventListener("click", () => {
            inputFile.value = "";
            previewImg.src = "";
            previewBox.style.display = "none";
        });
    }

    // === XÓA BÌNH LUẬN (AJAX) ===
    document.addEventListener("click", function (e) {
        const delBtn = e.target.closest(".delete-comment-btn");
        if (!delBtn) return;

        e.preventDefault();
        if (!confirm("Bạn có chắc muốn xóa bình luận này?")) return;

        const id = delBtn.dataset.id;
        if (!id) return;

        const url = `${routes.commentList}/${id}`; // dùng routeList hoặc routeDestroy tùy setup
        fetch(url, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
        })
            .then(async (res) => {
                if (!res.ok) {
                    const text = await res.text();
                    console.error("Server error:", text);
                    throw new Error("Server returned " + res.status);
                }
                return res.json();
            })
            .then((data) => {
                if (data.success) {
                    const item = document.getElementById(`comment-${id}`);
                    if (item) item.remove();
                } else {
                    alert(data.message || "❌ Xóa bình luận thất bại!");
                }
            })
            .catch((err) => console.error("⚠️ Lỗi khi xóa bình luận:", err));
    });

    // === OVERLAY ===
    const commentOverlay = document.getElementById("commentOverlay");
    if (commentOverlay) {
        commentOverlay.addEventListener("click", function (e) {
            if (e.target.id === "commentOverlay") closeCommentOverlay();
        });
    }

    function openCommentOverlay(postId) {
        const overlay = document.getElementById("commentOverlay");
        overlay.style.display = "flex";
        document.getElementById("commentPostId").value = postId;
        loadComments(postId);
    }

    function closeCommentOverlay() {
        const overlay = document.getElementById("commentOverlay");
        overlay.style.display = "none";
        pauseAllVideos();
    }

    function pauseAllVideos() {
        document.querySelectorAll("video").forEach((v) => {
            if (!v.paused) v.pause();
        });
    }

    // === LOAD COMMENTS ===
    function loadComments(postId) {
        showSpinner();
        fetch(`${routes.commentList}/${postId}`)
            .then((res) => res.text())
            .then((html) => {
                const area = document.getElementById("comments-area");
                area.innerHTML = html;
                hideSpinner();
            })
            .catch((err) => {
                console.error("⚠️ Lỗi khi load bình luận:", err);
                hideSpinner();
            });
    }

    // === SPINNER ===
    function showSpinner() {
        const area = document.getElementById("comments-area");
        area.innerHTML = `
            <div id="loading-spinner" class="loading-spinner">
                <div class="spinner"></div>
                <p>Đang tải bình luận...</p>
            </div>`;
    }

    function hideSpinner() {
        const spinner = document.getElementById("loading-spinner");
        if (spinner) spinner.remove();
    }
    // === XEM ẢNH BÌNH LUẬN PHÓNG TO ===
    document.addEventListener("click", function (e) {
        const img = e.target.closest(".imagebl");
        if (!img) return;

        const overlay = document.getElementById("imageOverlaybl");
        const overlayImg = document.getElementById("overlayImagebl");

        if (overlay && overlayImg) {
            overlayImg.src = img.src;
            overlay.style.display = "flex";
        }
    });

    document.addEventListener("click", function (e) {

        if (e.target.id === "imageOverlaybl") {
            document.getElementById("imageOverlaybl").style.display = "none";
        }
    });


});
