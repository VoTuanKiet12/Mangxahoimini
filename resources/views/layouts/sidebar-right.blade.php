<button class="sidebar-toggle" id="sidebarToggle">
    &#9776;
</button>

<aside class="sidebar-right" id="sidebarRight">
    {{-- Lời mời kết bạn --}}
    <div class="box-invite">
        <a href="{{ route('ketban.loimoi') }}" style="text-decoration: none; color: inherit;">
            <h3 style="cursor: pointer;">Lời mời kết bạn</h3>
        </a>
        @forelse($requests as $req)
        <a href="{{ route('user.show', $req->user->id) }}" style="text-decoration: none;">
            <div class=" invite-item">
                <img src="{{ $req->user->anh_dai_dien 
                        ? asset('storage/app/public/' . $req->user->anh_dai_dien) 
                        : asset('public/uploads/default.png') }}"
                    alt="avatar" class="avatar-invite">

                <p class="invite-name">{{ $req->user->name ?? $req->user->username }}</p>

                <div class="invite-actions">
                    <form method="POST" action="{{ route('ketban.accept', $req->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-accept">Xác nhận</button>
                    </form>
                    <form method="POST" action="{{ route('ketban.decline', $req->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-decline">Từ chối</button>
                    </form>
                </div>
            </div>
        </a>
        @empty
        <p>Không có lời mời kết bạn</p>
        @endforelse
    </div>

    {{-- Gợi ý bạn bè --}}
    <div class="box-suggest">
        <a href="{{ route('ketban.goi_y') }}" style="text-decoration: none; color: inherit;">
            <h3 style="cursor: pointer;">Gợi ý bạn bè</h3>
        </a>
        @forelse($suggestions as $sg)
        <a href="{{ route('user.show', $sg->id) }}" style="text-decoration: none;">

            <div class="suggest-item">
                {{-- Thẻ a bao toàn bộ avatar + tên --}}
                <img src="{{ $sg->anh_dai_dien 
                    ? asset('storage/app/public/' . $sg->anh_dai_dien) 
                    : asset('public/uploads/default.png') }}"
                    alt="{{ $sg->name ?? $sg->username }}"
                    class="avatar-suggest">

                <p class="suggest-name">
                    <strong>{{ $sg->name ?? $sg->username }}</strong>
                    @if(!empty($sg->mutual_count) && $sg->mutual_count > 0)
                    <br><small class="mutual-count">({{ $sg->mutual_count }} bạn chung)</small>
                    @endif
                </p>


                {{-- Nút kết bạn tách riêng --}}
                <form method="POST" action="{{ route('ketban.send', $sg->id) }}">
                    @csrf
                    <button type="submit" class="btn-add-friend">Kết bạn</button>
                </form>

            </div>
        </a>
        @empty
        <p>Không còn gợi ý nào</p>
        @endforelse
    </div>

    {{-- Danh sách bạn bè --}}
    <div class="box-friends">
        <a href="{{ route('ketban.ban_be') }}" style="text-decoration: none; color: inherit;">
            <h3 style="cursor: pointer;">Bạn bè</h3>
        </a>
        <ul class="friends-list">
            @forelse($friends as $fr)
            @php
            $banbe = $fr->user_id == Auth::id() ? $fr->banBe : $fr->user;
            @endphp
            <li class="friend-item"
                data-friend-id="{{ $banbe->id }}"
                data-friend-name="{{ $banbe->name ?? $banbe->username }}"
                data-friend-avatar="{{ $banbe->anh_dai_dien 
                    ? asset('storage/app/public/' . $banbe->anh_dai_dien) 
                    : asset('public/uploads/default.png') }}">
                <img src="{{ $banbe->anh_dai_dien 
                            ? asset('storage/app/public/' . $banbe->anh_dai_dien) 
                            : asset('public/uploads/default.png') }}"
                    alt="avatar" class="avatar-friend">
                <span class="friend-name">{{ $banbe->name ?? $banbe->username }}</span>
            </li>

            @empty
            <li class="friend-item">Bạn chưa có bạn bè nào</li>
            @endforelse
        </ul>
    </div>
</aside>
@include('tinnhan.chatbox')
<div id="themeOverlay" class="theme-overlay hidden">
    <div class="theme-content">
        <h3>Chọn ảnh nền chat</h3>
        <div class="preset-bg-list">
            <figure class="preset-item">
                <button id="clearChatBackground" class="clear-bg-btn">
                    <i class="bi bi-x-circle"></i> Xóa ảnh nền
                </button>
            </figure>
            <figure class="preset-item">
                <img src="{{ asset('storage/app/public/chat-backgrounds/bg1.jpg') }}" class="preset-bg">
                <figcaption>Rừng cây</figcaption>
            </figure>
            <figure class="preset-item">
                <img src="{{ asset('storage/app/public/chat-backgrounds/bg2.jpg') }}" class="preset-bg">
                <figcaption>Hố đen</figcaption>
            </figure>
            <figure class="preset-item">
                <img src="{{ asset('storage/app/public/chat-backgrounds/bg3.jpg') }}" class="preset-bg">
                <figcaption>Đêm tối</figcaption>
            </figure>
            <figure class="preset-item">
                <img src="{{ asset('storage/app/public/chat-backgrounds/bg4.jpg') }}" class="preset-bg">
                <figcaption>Hoạt hình</figcaption>
            </figure>
            <figure class="preset-item">
                <img src="{{ asset('storage/app/public/chat-backgrounds/bg5.jpg') }}" class="preset-bg">
                <figcaption>Lâu đài</figcaption>
            </figure>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Container để chứa tất cả các hộp chat
        const chatContainer = document.createElement("div");
        chatContainer.id = "chatContainer";
        Object.assign(chatContainer.style, {
            position: "fixed",
            bottom: "0",
            right: "20px",
            display: "flex",
            gap: "10px",
            zIndex: "999"
        });
        document.body.appendChild(chatContainer);

        window.chatContainer = chatContainer; // cho các script khác dùng
    });
</script>
<script>
    window.loadChatMessages = function(chatMessages, friendId) {
        fetch(`{{ url('tin-nhan') }}/${friendId}`)
            .then(res => res.text())
            .then(html => {
                const currentScroll = chatMessages.scrollTop;
                const isNearBottom =
                    chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight < 100;

                // ⚡ Chỉ cập nhật nếu khác nội dung
                if (chatMessages.innerHTML.trim() !== html.trim()) {
                    chatMessages.innerHTML = html;

                    // ✅ Chỉ auto scroll nếu đang ở gần cuối
                    if (isNearBottom) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        // Giữ nguyên vị trí cuộn cũ nếu người dùng đang xem tin nhắn cũ
                        chatMessages.scrollTop = currentScroll;
                    }
                }
            })
            .catch(() => {
                console.error("Không thể tải tin nhắn.");
            });
    };
</script>

<script>
    window.applyChatStyle = function(chatBox, chatMessages, chatColorInput, data) {
        const color = data.color || "#0084ff";
        chatBox.style.setProperty("--my-chat-color", color);
        chatColorInput.value = color;

        // Hiển ảnh nền nếu có
        if (data.background) {
            chatMessages.style.backgroundImage = `url(${data.background})`;
            chatMessages.style.backgroundSize = "cover";
            chatMessages.style.backgroundPosition = "center";
        } else {
            chatMessages.style.backgroundImage = "none";
        }
    };
</script>
<script>
    window.createChatBox = function(friendId, name, avatar) {
        const existing = document.querySelector(`.chat-box[data-id="${friendId}"]`);
        if (existing) return;

        const base = document.getElementById("chatBox");
        const chatBox = base.cloneNode(true);
        chatBox.classList.remove("hidden");
        chatBox.id = "";
        chatBox.dataset.id = friendId;

        const chatName = chatBox.querySelector("#chatName");
        const chatAvatar = chatBox.querySelector("#chatAvatar");
        const chatFriendId = chatBox.querySelector("#chatFriendId");
        const chatMessages = chatBox.querySelector("#chatMessages");
        const chatInput = chatBox.querySelector("#chatInput");
        const chatForm = chatBox.querySelector("#chatForm");
        const chatClose = chatBox.querySelector("#chatClose");
        const chatOptions = chatBox.querySelector("#chatOptions");
        const chatOverlay = chatBox.querySelector("#chatOverlay");
        const chatColorInput = chatBox.querySelector("#chatColor");
        const chatBgInput = chatBox.querySelector("#chatBgInput");
        const resetChatBg = chatBox.querySelector("#resetChatBg");
        const chooseImage = chatBox.querySelector("#chooseImage");
        const chatImage = chatBox.querySelector("#chatImage");
        const preview = chatBox.querySelector("#imagePreview");
        const previewImg = chatBox.querySelector("#previewImg");
        const cancelPreview = chatBox.querySelector("#cancelPreview");

        chatName.textContent = name;
        chatAvatar.src = avatar;
        chatFriendId.value = friendId;
        chatContainer.appendChild(chatBox);

        // Lấy màu chat
        fetch(`{{ url('lay-mau-chat') }}/${friendId}`)
            .then(res => res.json())
            .then(data => applyChatStyle(chatBox, chatMessages, chatColorInput, data))
            .catch(() => applyChatStyle(chatBox, chatMessages, chatColorInput, {
                color: "#0084ff"
            }));

        // Gọi load messages
        loadChatMessages(chatMessages, friendId);
        const interval = setInterval(() => {
            if (!document.body.contains(chatBox)) {
                clearInterval(interval);
                return;
            }
            loadChatMessages(chatMessages, friendId);
        }, 5000);


        // Lưu các biến cho script khác
        chatBox.dataset.interval = interval;
        window.setupChatEvents(chatBox, {
            friendId,
            chatMessages,
            chatInput,
            chatForm,
            chatImage,
            preview,
            previewImg,
            chatColorInput,
            chatBgInput,
            resetChatBg,
            chatClose,
            chatOptions,
            chatOverlay
        });
    };
</script>
<script>
    window.setupChatEvents = function(chatBox, refs) {
        const {
            friendId,
            chatMessages,
            chatInput,
            chatForm,
            chatImage,
            preview,
            previewImg,
            chatColorInput,
            chatBgInput,
            resetChatBg,
            chatClose,
            chatOptions,
            chatOverlay
        } = refs;

        // === Preview ảnh gửi ===
        const chooseImage = chatBox.querySelector("#chooseImage");
        const cancelPreview = chatBox.querySelector("#cancelPreview");

        chooseImage.addEventListener("click", () => chatImage.click());
        chatImage.addEventListener("change", () => {
            const file = chatImage.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                preview.classList.remove("hidden");
            };
            reader.readAsDataURL(file);
        });
        cancelPreview.addEventListener("click", () => {
            chatImage.value = "";
            preview.classList.add("hidden");
        });

        // === Gửi tin nhắn ===
        chatForm.addEventListener("submit", e => {
            e.preventDefault();
            const msg = chatInput.value.trim();
            const file = chatImage.files[0] || null;
            if (!msg && !file) return;

            const formData = new FormData();
            formData.append("friend_id", friendId);
            formData.append("noi_dung", msg || "");
            if (file) formData.append("hinh_anh", file);

            fetch(`{{ url('tin-nhan/gui') }}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData,
                })
                .then(res => res.status === 204 ? "" : res.text())
                .then(html => {
                    if (html.trim()) {
                        chatMessages.insertAdjacentHTML("beforeend", html);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                    chatInput.value = "";
                    chatImage.value = "";
                    preview.classList.add("hidden");
                    duaBanLenDau(friendId);
                });
        });

        // === Màu chat ===
        chatColorInput.addEventListener("input", () => {
            const color = chatColorInput.value;
            chatBox.style.setProperty("--my-chat-color", color);
            fetch(`{{ url('luu-mau-chat') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    friend_id: friendId,
                    color
                }),
            });
        });

        // === Ảnh nền (chủ đề chat) ===
        const openThemeSelector = chatBox.querySelector("#openThemeSelector");
        if (openThemeSelector) {
            openThemeSelector.addEventListener("click", () => {
                console.log("Mở overlay chọn chủ đề cho friend:", friendId);
                const themeOverlay = document.getElementById("themeOverlay");

                if (!themeOverlay) {
                    console.error("Không tìm thấy #themeOverlay");
                    return;
                }

                // Hiện overlay
                themeOverlay.classList.remove("hidden");
                themeOverlay.style.display = "flex";

                // 👉 Click ra ngoài để đóng overlay
                const overlayClickHandler = (e) => {
                    if (e.target === themeOverlay) {
                        themeOverlay.classList.add("hidden");
                        themeOverlay.style.display = "none";
                        themeOverlay.removeEventListener("click", overlayClickHandler);
                    }
                };
                themeOverlay.addEventListener("click", overlayClickHandler);

                // Nút Đóng

                // Gán click cho từng ảnh nền
                themeOverlay.querySelectorAll(".preset-bg").forEach(bg => {
                    bg.onclick = () => {
                        const url = bg.src;
                        console.log("Ảnh nền được chọn:", url);

                        chatMessages.style.backgroundImage = `url(${url})`;
                        chatMessages.style.backgroundSize = "cover";
                        chatMessages.style.backgroundPosition = "center";

                        fetch(`{{ url('luu-anh-nen-chat') }}`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    ban_be_id: friendId,
                                    anh_nen: url
                                })
                            })
                            .then(r => console.log("Đã lưu ảnh nền:", r.status));
                        themeOverlay.classList.add("hidden");
                        themeOverlay.style.display = "none";
                        themeOverlay.removeEventListener("click", overlayClickHandler);
                    };
                });
                const clearBtn = document.getElementById("clearChatBackground");
                if (clearBtn) {
                    clearBtn.onclick = () => {
                        chatMessages.style.backgroundImage = "none";

                        fetch(`{{ url('xoa-anh-nen-chat') }}`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    ban_be_id: friendId
                                })
                            })
                            .then(r => console.log("Đã xóa ảnh nền:", r.status));

                        // Ẩn overlay
                        const themeOverlay = document.getElementById("themeOverlay");
                        themeOverlay.classList.add("hidden");
                        themeOverlay.style.display = "none";
                    };
                }
            });
        }

        // === Đóng hộp chat ===
        chatClose.addEventListener("click", () => {
            clearInterval(chatBox.dataset.interval);
            chatBox.remove();
        });

        // === Hiện / ẩn tùy chọn ===
        chatOptions.addEventListener("click", e => {
            e.stopPropagation();
            chatOverlay.classList.toggle("show");
        });
        document.addEventListener("click", e => {
            if (!chatOverlay.contains(e.target) && e.target !== chatOptions) {
                chatOverlay.classList.remove("show");
            }
        });
    };
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".friend-item").forEach(item => {
            item.addEventListener("click", () => {
                const friendId = item.dataset.friendId;
                const name = item.dataset.friendName;
                const avatar = item.dataset.friendAvatar;
                const existingBox = document.querySelector(`.chat-box[data-id="${friendId}"]`);
                if (existingBox) {
                    existingBox.remove();
                    return;
                }
                createChatBox(friendId, name, avatar);
                moChat(friendId);
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const overlay = document.getElementById("imagechatOverlay");
        const overlayImg = document.getElementById("overlayImgchat");

        // Khi click vào ảnh trong tin nhắn
        document.body.addEventListener("click", (e) => {
            const target = e.target;
            if (target.classList.contains("chat-img")) {
                overlayImg.src = target.src;
                overlay.style.display = "flex";
            }
        });

        // Khi click ra ngoài ảnh thì tắt overlay
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) {
                overlay.style.display = "none";
                overlayImg.src = "";
            }
        });
    });
</script>
<script>
    // 🟢 Hàm kiểm tra tin nhắn mới
    function kiemTraTinMoi() {
        fetch('{{ url("kiemtra-tinnhan-moi") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Laravel hiểu đây là AJAX
                }
            })
            .then(res => {
                // Nếu bị logout (trả về 401 hoặc redirect HTML)
                if (!res.ok || res.headers.get('content-type')?.includes('text/html')) {
                    console.warn('Người dùng chưa đăng nhập hoặc session hết hạn.');
                    clearInterval(window._tinNhanInterval);
                    return [];
                }
                return res.json();
            })
            .then(data => {
                if (!Array.isArray(data)) return; // tránh lỗi nếu không phải JSON

                document.querySelectorAll('.friend-item').forEach(item => {
                    const friendId = parseInt(item.dataset.friendId);
                    let bell = item.querySelector('.chat-bell');

                    if (data.includes(friendId)) {
                        if (!bell) {
                            bell = document.createElement('i');
                            bell.className = 'bi bi-bell-fill chat-bell';
                            item.appendChild(bell);
                        }
                        duaBanLenDau(friendId);
                    } else if (bell) {
                        bell.remove();
                    }
                });
            })
            .catch(err => {
                console.error('Lỗi fetch tin nhắn:', err);
                clearInterval(window._tinNhanInterval);
            });
    }

    // 🟢 Chỉ chạy nếu người dùng đang đăng nhập
    @if(Auth::check())
    window._tinNhanInterval = setInterval(kiemTraTinMoi, 2000); // 3 giây là hợp lý
    @endif

    // 🟢 Khi mở chat, đánh dấu đã đọc
    function moChat(friendId) {
        fetch('{{ url("danhdau-dadoc") }}/' + friendId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => {
                const item = document.querySelector(`.friend-item[data-friend-id="${friendId}"]`);
                const dot = item?.querySelector('.green-dot');
                if (dot) dot.remove();
            })
            .catch(err => console.error('Lỗi đánh dấu đã đọc:', err));
    }

    const lastMessageTimes = new Map();

    function duaBanLenDau(friendId) {
        const list = document.querySelector('.friends-list');
        const items = Array.from(list.querySelectorAll('.friend-item'));
        const now = Date.now();

        // Cập nhật thời gian nhắn mới nhất cho friendId này
        lastMessageTimes.set(friendId, now);

        // Sắp xếp danh sách theo thời gian nhắn mới nhất
        items.sort((a, b) => {
            const timeA = lastMessageTimes.get(parseInt(a.dataset.friendId)) || 0;
            const timeB = lastMessageTimes.get(parseInt(b.dataset.friendId)) || 0;
            return timeB - timeA;
        });

        // Cập nhật lại thứ tự DOM theo sắp xếp
        items.forEach(item => list.appendChild(item));
    }
</script>
<script>
    document.addEventListener("click", function(e) {
        const btn = e.target.closest(".delete-msg-btn");
        if (!btn) return;

        const msgId = btn.dataset.id;
        if (!confirm("Bạn có chắc muốn xóa tin nhắn này?")) return;

        fetch(`{{ url('tin-nhan/xoa') }}/${msgId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const msg = document.querySelector(`.msg[data-msg-id="${msgId}"]`);
                    if (msg) msg.remove();
                } else {
                    alert("Không thể xóa tin nhắn.");
                }
            })
            .catch(err => console.error("Lỗi khi xóa tin nhắn:", err));
    });
</script>