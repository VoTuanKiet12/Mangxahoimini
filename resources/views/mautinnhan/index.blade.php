<h1>Mẫu tin nhắn</h1>
@foreach($templates as $t)
<div>
    Người gửi: {{ $t->nguoi_gui }}, Người nhận: {{ $t->nguoi_nhan }}
    (Màu: {{ $t->mau_nen }}, Hình: {{ $t->hinh_nen }})
</div>
@endforeach