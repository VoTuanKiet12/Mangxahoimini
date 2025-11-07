@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/nhomct.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/nhomchat.css') }}">
@section('title', $nhom->ten_nhom)

@section('full')

<div class="group-detail">
    <div class="group-container">

        <div class="group-left">
            <div class="group-info">
                <div class="group-header">
                    <h2>{{ $nhom->ten_nhom }}</h2>
                    <p><strong>Người tạo:</strong> {{ $nhom->chuNhom->name ?? 'Không rõ' }}</p>
                </div>

                @php
                $isMember = $nhom->users->contains(auth()->id());
                $isOwner = $nhom->nguoi_tao_id === auth()->id();
                @endphp

                @if ($isMember && !$isOwner)
                <form action="{{ route('nhom.leave', $nhom->id) }}" method="POST" style="margin-top:15px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-leave"
                        onclick="return confirm('Bạn có chắc chắn muốn rời khỏi nhóm này không?')">
                        Rời nhóm
                    </button>
                </form>
                @endif


                <div class="voice-chat-section">
                    <h3>Đàm thoại nhóm</h3>
                    <div id="voiceStatus" class="voice-status">Chưa tham gia</div>

                    <div class="voice-controls">
                        <button id="joinVoiceBtn" class="btn-voice join">Tham gia hội thoại</button>
                        <button id="leaveVoiceBtn" class="btn-voice leave" style="display:none;">Rời hội thoại</button>
                    </div>

                    <div id="voiceMembers" class="voice-members"></div>

                    <div id="myControls" hidden>
                        <button id="mute-mic" class="btn-voice mute"><i class="bi bi-mic-fill"></i></button>
                        <button id="mute-audio" class="btn-voice audio"><i class="bi bi-volume-up-fill"></i></button>
                        <progress id="mic-level" value="0" max="255" style="width:100%;"></progress>
                    </div>
                </div>



                <div class="group-switch">


                </div>
            </div>
        </div>


        <div class="group-center">
            <div id="videoSection" class="video-section" hidden>
                <h4>Video nhóm</h4>
                <div id="videoGrid" class="video-grid"></div>
            </div>


            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="chat-container">
                <h3>Tin nhắn nhóm</h3>
                <div id="messages" class="chat-box"></div>


                <form id="messageForm" enctype="multipart/form-data" method="POST" action="javascript:void(0);">
                    <div id="imagePreviewContainer">
                        <img id="previewImage" src="" alt="Preview">
                        <button type="button" id="removeImageBtnnhom"><i class="bi bi-x-circle-fill"></i></button>
                    </div>

                    @csrf
                    <input type="text" id="messageInput" placeholder="Nhập tin nhắn..." name="noi_dung">
                    <input type="file" id="imageInput" name="anh" accept="image/*" style="display:none;">
                    <label for="imageInput" class="image-icon">
                        <i class="bi bi-image"></i>
                    </label>
                    <button type="submit">Gửi</button>
                </form>
            </div>

            <div id="chatBox" data-room-id="{{ $nhom->id }}"></div>
        </div>


        <div class="group-right">
            <div class="member-section">
                <div class="group-header1">
                    <h2>Thành viên nhóm ({{ $nhom->users->where('pivot.trang_thai', 'tham_gia')->count() }})</h2>
                    @php
                    $currentVaiTro = auth()->user()->nhom()->where('nhom_id', $nhom->id)->first()?->pivot->vai_tro;
                    @endphp

                    @if (in_array($currentVaiTro, ['chu_nhom', 'quan_tri_vien']))
                    <a href="{{ route('nhom.quanlynhom', $nhom->id) }}" class="btn-edit-group">
                        <i class="bi bi-gear-fill"></i>
                    </a>
                    @endif
                </div>

                <div class="member-grid">
                    @php
                    $thanhVienThamGia = $nhom->users
                    ->filter(fn($u) => $u->pivot->trang_thai === 'tham_gia')
                    ->sortBy('pivot.ngay_tham_gia');
                    @endphp

                    @forelse ($thanhVienThamGia as $user)
                    <div class="member-card">
                        <img src="{{ $user->anh_dai_dien 
                                ? asset('storage/app/public/' . $user->anh_dai_dien) 
                                : asset('public/uploads/default.png') }}" alt="Avatar">
                        <div class="name">{{ $user->name }}</div>
                        <div class="role">{{ ucfirst($user->pivot->vai_tro) }}</div>
                    </div>
                    @empty
                    <p class="no-member">Hiện nhóm chưa có thành viên nào tham gia.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const roomId = document.getElementById("chatBox").dataset.roomId;
    const getMessagesUrl = "{{ route('nhom.getMessages', ':id') }}".replace(':id', roomId);
    const sendMessageUrl = "{{ route('nhom.sendMessage', ':id') }}".replace(':id', roomId);

    function loadMessages() {
        fetch(getMessagesUrl)
            .then(res => res.json())
            .then(data => {
                const box = document.getElementById('messages');
                box.innerHTML = "";
                data.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = "msg";
                    div.dataset.msgId = msg.id;

                    let html = `<strong>${msg.nguoi_gui?.name || 'Ẩn danh'}:</strong> `;
                    if (msg.noi_dung) html += msg.noi_dung;
                    if (msg.anh)
                        html += `<br><img src="{{ asset('public/storage') }}/${msg.anh}" class="chat-img" alt="Ảnh tin nhắn">`;

                    if (msg.co_the_xoa)
                        html += `<button class="btnxoa" data-id="${msg.id}"><i class="bi bi-trash-fill"></i></button>`;

                    div.innerHTML = html;
                    box.appendChild(div);
                });
            })
            .catch(err => console.error("❌ Lỗi tải tin nhắn:", err));
    }

    document.getElementById('messageForm').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch(sendMessageUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                },
                body: formData
            })
            .then(res => res.json())
            .then(() => {
                e.target.reset();
                loadMessages();
            })
            .catch(err => console.error("❌ Lỗi gửi tin nhắn:", err));
    });

    document.addEventListener("click", function(e) {
        const btn = e.target.closest(".btnxoa");
        if (!btn) return;
        const msgId = btn.dataset.id;
        if (!confirm("Bạn có chắc muốn xóa tin nhắn này?")) return;

        fetch(`{{ url('/nhom/tin-nhan/xoa') }}/${msgId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            })
            .then(async res => {
                const text = await res.text();
                let data = {};
                try {
                    data = JSON.parse(text);
                } catch {}
                if (!res.ok) throw new Error(data.error || `HTTP ${res.status}`);

                if (data.success) {
                    document.querySelector(`.msg[data-msg-id="${msgId}"]`)?.remove();
                } else alert(data.error || "Không thể xóa tin nhắn.");
            })
            .catch(err => {
                console.error("❌ Lỗi khi xóa tin nhắn:", err);
                alert("Lỗi khi xóa tin nhắn!");
            });
    });

    loadMessages();
    setInterval(loadMessages, 10000);
</script>


<script>
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const removeImageBtnnhom = document.getElementById('removeImageBtnnhom');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                removeImageBtnnhom.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeImageBtnnhom.addEventListener('click', function() {
        imageInput.value = '';
        previewImage.src = '';
        previewImage.style.display = 'none';
        removeImageBtnnhom.style.display = 'none';
    });
</script>




<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://unpkg.com/simple-peer@9.11.1/simplepeer.min.js"></script>

<script>
    if (!window.__voice_chat_initialized__) {
        window.__voice_chat_initialized__ = true;

        (function() {
            const SIGNALING = "http://127.0.0.1:3000";
            const room = "group_{{ $nhom->id }}";
            const myUsername = "{{ auth()->user()->name }}";
            const myAvatar = "{{ auth()->user()->anh_dai_dien ? asset('public/storage/' . auth()->user()->anh_dai_dien) : asset('public/uploads/default.png') }}";

            const joinBtn = document.getElementById('joinVoiceBtn');
            const leaveBtn = document.getElementById('leaveVoiceBtn');
            const statusEl = document.getElementById('voiceStatus');
            const membersEl = document.getElementById('voiceMembers');
            const myControls = document.getElementById('myControls');
            const muteBtn = document.getElementById('mute-mic');
            const micLevelEl = document.getElementById('mic-level');

            let socket = null;
            let mySocketId = null;
            let localStream = null;
            let peers = {};
            let peerNames = {};
            let peerAvatars = {};
            let localAudioTrack = null;
            let micMuted = false;

            /** 🟢 Khởi tạo kết nối Socket và sự kiện */
            function initSocket() {
                socket = io(SIGNALING, {
                    transports: ['websocket', 'polling']
                });

                socket.on('connect', () => {
                    mySocketId = socket.id;
                    socket.emit('join', {
                        room,
                        username: myUsername,
                        avatar: myAvatar
                    });
                });

                socket.on('existing-peers', list => {
                    list.forEach(p => {
                        if (!p || p.id === mySocketId || peers[p.id]) return;
                        peerNames[p.id] = p.username;
                        peerAvatars[p.id] = p.avatar;
                        peers[p.id] = createPeer(p.id, true);
                    });
                });

                socket.on('new-peer', ({
                    id,
                    username,
                    avatar
                }) => {
                    if (!id || id === mySocketId || peers[id]) return;
                    peerNames[id] = username;
                    peerAvatars[id] = avatar;
                    peers[id] = createPeer(id, false);
                });

                socket.on('signal', ({
                    from,
                    signal,
                    username,
                    avatar
                }) => {
                    if (!from) return;
                    peerNames[from] = username;
                    peerAvatars[from] = avatar;
                    if (!peers[from]) peers[from] = createPeer(from, false);
                    peers[from].signal(signal);
                });

                socket.on('peer-disconnect', id => {
                    if (peers[id]) peers[id].destroy();
                    delete peers[id];
                    removePeerUI(id);
                });
            }

            /** 🎤 Tham gia hội thoại */
            joinBtn.addEventListener('click', async () => {
                try {
                    localStream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });
                    localAudioTrack = localStream.getAudioTracks()[0];

                    // 🔹 Theo dõi mức mic
                    const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    const analyser = audioContext.createAnalyser();
                    const source = audioContext.createMediaStreamSource(localStream);
                    const dataArray = new Uint8Array(analyser.frequencyBinCount);
                    source.connect(analyser);

                    function updateMicLevel() {
                        analyser.getByteTimeDomainData(dataArray);
                        let sum = 0;
                        for (let i = 0; i < dataArray.length; i++) {
                            const v = dataArray[i] - 128;
                            sum += v * v;
                        }
                        const rms = Math.sqrt(sum / dataArray.length);
                        micLevelEl.value = Math.min(255, rms * 10);
                        requestAnimationFrame(updateMicLevel);
                    }
                    updateMicLevel();

                    // 🔹 Kết nối socket
                    if (!socket) initSocket();

                    // UI
                    statusEl.textContent = "Đang tham gia hội thoại...";
                    joinBtn.style.display = "none";
                    leaveBtn.style.display = "inline-block";
                    myControls.hidden = false;

                    // Hiển thị bản thân
                    const meDiv = document.createElement('div');
                    meDiv.className = 'voice-member';
                    meDiv.id = 'member-me';
                    meDiv.innerHTML = `
                    <img src="${myAvatar}" alt="avatar">
                    <div class="name">${myUsername} (Tôi)</div>`;
                    membersEl.appendChild(meDiv);
                } catch (err) {
                    alert("Không thể truy cập micro: " + err.message);
                }
            });

            /** 🚪 Rời hội thoại */
            leaveBtn.addEventListener('click', () => {
                if (localStream) localStream.getTracks().forEach(t => t.stop());
                Object.values(peers).forEach(p => p.destroy());
                peers = {};
                membersEl.innerHTML = '';
                statusEl.textContent = "Chưa tham gia";
                joinBtn.style.display = "inline-block";
                leaveBtn.style.display = "none";
                myControls.hidden = true;

                if (socket) {
                    socket.emit('leave', {
                        room
                    });
                    socket.disconnect();
                    socket = null;
                }
            });

            /** 🎧 Tạo Peer */
            function createPeer(id, initiator = false) {
                if (!localStream) return null;
                const peer = new SimplePeer({
                    initiator,
                    trickle: false,
                    stream: localStream,
                    config: {
                        iceServers: [{
                            urls: 'stun:stun.l.google.com:19302'
                        }]
                    }
                });
                peer.on('stream', stream => addAudioElement(id, stream, peerNames[id]));
                peer.on('signal', signal => {
                    socket.emit('signal', {
                        to: id,
                        signal,
                        username: myUsername,
                        avatar: myAvatar
                    });
                });
                peer.on('close', () => removePeerUI(id));
                peer.on('error', e => console.error('peer error', e));
                return peer;
            }

            /** 🎙️ Thêm audio của peer */
            function addAudioElement(id, stream, username) {
                const avatar = peerAvatars[id] || "{{ asset('public/uploads/default.png') }}";
                let div = document.getElementById('member-' + id);
                if (!div) {
                    div = document.createElement('div');
                    div.className = 'voice-member';
                    div.id = 'member-' + id;
                    div.innerHTML = `<img src="${avatar}" alt="avatar"><div class="name">${username || 'Ẩn danh'}</div>`;
                    const audio = document.createElement('audio');
                    audio.autoplay = true;
                    audio.playsInline = true;
                    audio.srcObject = stream;
                    div.appendChild(audio);
                    membersEl.appendChild(div);
                }
            }

            function removePeerUI(id) {
                const el = document.getElementById('member-' + id);
                if (el) el.remove();
            }

            /** 🎚️ Tắt / bật mic */
            muteBtn.addEventListener('click', () => {
                if (!localAudioTrack) return;
                micMuted = !micMuted;
                localAudioTrack.enabled = !micMuted;
                muteBtn.innerHTML = micMuted ?
                    '<i class="bi bi-mic-mute-fill"></i>' :
                    '<i class="bi bi-mic-fill"></i>';
                muteBtn.classList.toggle('active', micMuted);
            });
            /** 🔇 Tắt / bật nghe */
            const muteAudioBtn = document.getElementById('mute-audio');
            let audioMuted = false;

            muteAudioBtn.addEventListener('click', () => {
                const audios = membersEl.querySelectorAll('audio');
                audios.forEach(a => a.muted = !audioMuted);
                audioMuted = !audioMuted;
                muteAudioBtn.innerHTML = audioMuted ?
                    '<i class="bi bi-volume-mute-fill"></i>' :
                    '<i class="bi bi-volume-up-fill"></i>';
                muteAudioBtn.classList.toggle('active', audioMuted);

            });

            window.addEventListener('beforeunload', () => {
                if (socket) socket.disconnect();
            });
        })();
    }
</script>


@endsection