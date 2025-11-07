@extends('doanhnghiep.quanly')

@section('title', 'Đăng bài viết doanh nghiệp')

@section('quanly')
<style>
    /* ====== FORM ĐĂNG BÀI DOANH NGHIỆP ====== */
    .post-form-container {
        max-width: 650px;
        margin: 30px auto;
        background: #fff;
        border-radius: 12px;
        padding: 25px 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-family: "Segoe UI", sans-serif;
    }

    .post-form-container h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #e8f8ef;
        border: 1px solid #b7ebc1;
        color: #1a7f3c;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .alert-danger {
        background: #fdeaea;
        border: 1px solid #f5c2c2;
        color: #a02121;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
    }

    textarea,
    select,
    input[type="file"] {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: all 0.2s ease;
        background-color: #fafafa;
    }

    textarea:focus,
    select:focus,
    input[type="file"]:focus {
        border-color: #007bff;
        outline: none;
        background: #fff;
    }

    button[type="submit"] {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
        width: 100%;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }
</style>

<div class="post-form-container">
    <h2>Đăng bài viết cho doanh nghiệp</h2>

    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('doanhnghiep.baiviet.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label class="form-label">Nội dung bài viết</label>
            <textarea name="noi_dung" rows="4" placeholder="Viết nội dung...">{{ old('noi_dung') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Gắn sản phẩm vào bài viết</label>
            <select name="san_pham_id">
                <option value="">-- Không chọn sản phẩm --</option>
                @foreach ($sanPhams as $sp)
                <option value="{{ $sp->id }}">{{ $sp->ten_san_pham }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Hình ảnh (tối đa 4)</label>
            <input type="file" name="hinh_anh[]" multiple accept="image/*">
        </div>

        <div class="form-group">
            <label class="form-label">Video (tối đa 10MB)</label>
            <input type="file" name="video" accept="video/*">
        </div>

        <button type="submit">Đăng bài</button>
    </form>
</div>
@endsection