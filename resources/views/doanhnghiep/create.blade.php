@extends('layouts.app')
@section('title', 'Đăng ký cửa hàng')

<link rel="stylesheet" href="{{ asset('public/css/doanhnghiepdk.css') }}">

@section('content')
<div class="register-container" data-aos="fade-down">
    <h3>Đăng ký cửa hàng</h3>

    <form action="{{ route('doanhnghiep.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Tên cửa hàng *</label>
            <input type="text" name="ten_cua_hang" required>
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="mo_ta"></textarea>
        </div>

        <div class="form-group">
            <label>Logo</label>
            <input type="file" name="logo">
        </div>

        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" name="dia_chi">
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="so_dien_thoai">
        </div>

        <button type="submit" class="btn-submit">Gửi đăng ký</button>
    </form>
</div>
<script>
    document.querySelector('input[name="logo"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const parent = e.target.parentNode;

        const oldPreview = parent.querySelector('.img-preview');
        if (oldPreview) oldPreview.remove();

        if (file) {
            const imgPreview = document.createElement('img');
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.classList.add('img-preview');
            imgPreview.style.maxWidth = '120px';
            imgPreview.style.marginTop = '10px';
            imgPreview.style.borderRadius = '8px';
            imgPreview.style.display = 'block';
            parent.appendChild(imgPreview);
        }
    });
</script>
@endsection