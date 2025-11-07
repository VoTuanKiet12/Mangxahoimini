@extends('doanhnghiep.quanly')
<link rel="stylesheet" href="{{ asset('public/css/sanphamsua.css') }}">
@section('title', 'Chỉnh sửa sản phẩm')

@section('quanly')
<div class="edit-container">
    <h2>Chỉnh sửa sản phẩm</h2>

    {{-- Hiển thị thông báo --}}
    @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert error">
        <ul>
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('doanhnghiep.sanpham.update', $sanPham->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="ten_san_pham">Tên sản phẩm:</label>
            <input type="text" name="ten_san_pham" id="ten_san_pham"
                value="{{ old('ten_san_pham', $sanPham->ten_san_pham) }}" required>
        </div>

        <div class="form-group">
            <label for="loai_id">Loại sản phẩm:</label>
            <select name="loai_id" id="loai_id" required>
                @foreach($loaiSanPham as $loai)
                <option value="{{ $loai->id }}" {{ $sanPham->loai_id == $loai->id ? 'selected' : '' }}>
                    {{ $loai->ten_loai }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="mo_ta">Mô tả:</label>
            <textarea name="mo_ta" id="mo_ta" rows="4">{{ old('mo_ta', $sanPham->mo_ta) }}</textarea>
        </div>

        <div class="form-group">
            <label for="gia">Giá (VNĐ):</label>
            <input type="number" name="gia" id="gia" value="{{ old('gia', $sanPham->gia) }}" min="0" step="1000" required>
        </div>

        <div class="form-group">
            <label for="so_luong">Số lượng:</label>
            <input type="number" name="so_luong" id="so_luong" value="{{ old('so_luong', $sanPham->so_luong) }}" min="0" required>
        </div>

        <div class="form-group">
            <label for="trang_thai">Trạng thái:</label>
            <select name="trang_thai" id="trang_thai" required>
                <option value="con_hang" {{ $sanPham->trang_thai == 'con_hang' ? 'selected' : '' }}>Còn hàng</option>
                <option value="het_hang" {{ $sanPham->trang_thai == 'het_hang' ? 'selected' : '' }}>Hết hàng</option>
                <option value="an" {{ $sanPham->trang_thai == 'an' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>
        <div class="form-group">
            <label>Ảnh hiện tại:</label>

            @php
            $images = json_decode($sanPham->hinh_anh, true);
            if (!is_array($images)) {
            $images = [];
            }
            @endphp

            @if(count($images) > 0)
            <div class="image-preview">
                @foreach($images as $img)
                <img src="{{ asset('public/storage/' . ltrim($img, '/')) }}" alt="Ảnh sản phẩm">
                @endforeach
            </div>
            @else
            <p class="text-muted">Chưa có ảnh hoặc ảnh lỗi định dạng.</p>
            @endif
        </div>


        <div class="form-group">
            <label>Cập nhật ảnh mới (tùy chọn):</label>
            <input type="file" name="hinh_anh[]" multiple accept="image/*">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-save"> Lưu thay đổi</button>
            <a href="{{ route('doanhnghiep.sanpham.index') }}" class="btn btn-cancel">⬅ Quay lại</a>
        </div>
    </form>
</div>


@endsection