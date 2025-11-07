@extends('doanhnghiep.quanly')

@section('title', 'Đăng sản phẩm')
<link rel="stylesheet" href="{{ asset('public/css/sanphamdang.css') }}">
@section('quanly')
<div class="product-container">
    <h2 class="page-title">Đăng sản phẩm mới</h2>

    {{-- Thông báo --}}
    @if(session('error'))
    <div class="alert error">{{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('sanpham.store') }}" method="POST" enctype="multipart/form-data" class="product-form">
        @csrf
        <div class="form-group">
            <label for="loai_id">Loại sản phẩm</label>
            <select name="loai_id" id="loai_id" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                @foreach($loaiSanPham as $loai)
                <option value="{{ $loai->id }}">{{ $loai->ten_loai }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="ten_san_pham">Tên sản phẩm</label>
            <input type="text" name="ten_san_pham" id="ten_san_pham" placeholder="Nhập tên sản phẩm..." required>
        </div>

        <div class="form-group">
            <label for="mo_ta">Mô tả sản phẩm</label>
            <textarea name="mo_ta" id="mo_ta" rows="4" placeholder="Nhập mô tả chi tiết..."></textarea>
        </div>

        <div class="form-group">
            <label for="hinh_anh">Hình ảnh sản phẩm</label>
            <input type="file" name="hinh_anh[]" id="hinh_anh" multiple>
            <small>Chọn nhiều ảnh nếu muốn (jpg, jpeg, png, tối đa 2MB/ảnh)</small>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="gia">Giá (VNĐ)</label>
                <input type="number" name="gia" id="gia" min="0" placeholder="Nhập giá..." required>
            </div>

            <div class="form-group half">
                <label for="so_luong">Số lượng</label>
                <input type="number" name="so_luong" id="so_luong" min="0" placeholder="Nhập số lượng..." required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-plus"></i> Đăng sản phẩm
            </button>
        </div>
    </form>
</div>


@endsection