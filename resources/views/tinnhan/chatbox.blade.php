<div id="chatBox" class="chat-box hidden">
    <div class="chat-header">
        <div class="chat-user">
            <img id="chatAvatar" src="" alt="avatar" class="chat-avatar">
            <span id="chatName"></span>
        </div>
        <div class="chat-tools">
            <button id="chatOptions" title="Tùy chọn">⋯</button>
            <button id="chatClose" title="Đóng">×</button>
        </div>
    </div>


    <div class="chat-messages" id="chatMessages"></div>


    <form id="chatForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="chatFriendId">



        <input type="text" id="chatInput" placeholder="Nhắn tin...">


        <button type="submit" class="sendMessage" title="Gửi tin nhắn"><i class="bi bi-arrow-right-circle-fill"></i></button>
    </form>


    <div id="imagePreview" class="chat-preview hidden">
        <img id="previewImg" src="">
        <button id="cancelPreview" type="button" title="Hủy">✖</button>
    </div>


    <div id="chatOverlay" class="chat-overlay">
        <h3>Tùy chỉnh chat</h3>

        <table class="overlay-table">
            <tr>
                <td>Chọn Màu:</td>
                <td><input type="color" id="chatColor" value="#0084ff"></td>
            </tr>
            <tr>
                <td>Gửi ảnh:</td>
                <td>
                    <input type="file" id="chatImage" accept="image/*" style="display:none;">
                    <button type="button" id="chooseImage" title="Chọn ảnh"><i class="bi bi-images"></i></button>
                </td>
            </tr>
            <tr>
                <td>Chọn nền:</td>
                <td>
                    <button type="button" id="openThemeSelector" class="theme-btn">
                        <i class="bi bi-image"></i>
                    </button>
                </td>
            </tr>

        </table>
    </div>

</div>


<div id="imagechatOverlay" style="
  display:none;
  position:fixed;
  top:0; left:0;
  width:100%; height:100%;
  background:rgba(0,0,0,0.8);
  justify-content:center;
  align-items:center;
  z-index:2000;
">
    <img id="overlayImgchat" src="" style="
    max-width:90%;
    max-height:90%;
    border-radius:10px;
    box-shadow:0 0 15px rgba(255,255,255,0.3);
  ">
</div>