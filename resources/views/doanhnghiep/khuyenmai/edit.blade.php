@extends('doanhnghiep.quanly')

@section('title', 'Chỉnh sửa khuyến mãi')

@section('quanly')
<style>
    .edit-container {
        max-width: 700px;
        margin: 30px auto;
        background: #fff;
        padding: 25px 35px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        font-family: "Segoe UI", sans-serif;
    }

    .edit-container h3 {
        text-align: center;
        color: #007bff;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #333;
    }

    input[type="text"],
    input[type="number"],
    input[type="datetime-local"],
    select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: 0.2s;
    }

    input:focus,
    select:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .btn-group {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }

    .btn {
        padding: 10px 22px;
        border-radius: 8px;
        border: none;
        font-size: 15px;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #e0e0e0;
        color: #333;
    }

    .btn-secondary:hover {
        background-color: #ccc;
    }

    .alert {
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 5px solid #28a745;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 5px solid #dc3545;
    }
</style>

<div class="edit-container">
    <h3> Chỉnh sửa khuyến mãi</h3>

    {{-- Thông báo --}}
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left:20px;">
            @foreach ($errors->all() as $error)
            <li> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('khuyenmai.update', $khuyenmai->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="ten_khuyen_mai">Tên khuyến mãi</label>
            <input type="text" name="ten_khuyen_mai" id="ten_khuyen_mai"
                value="{{ old('ten_khuyen_mai', $khuyenmai->ten_khuyen_mai) }}" required>
        </div>

        <div class="form-group">
            <label for="loai_ap_dung">Áp dụng cho</label>
            <select name="loai_ap_dung" id="loai_ap_dung" required>
                <option value="san_pham" {{ $khuyenmai->loai_ap_dung == 'san_pham' ? 'selected' : '' }}>Sản phẩm</option>
                <option value="loai_san_pham" {{ $khuyenmai->loai_ap_dung == 'loai_san_pham' ? 'selected' : '' }}>Loại sản phẩm</option>
            </select>
        </div>

        <div class="form-group" id="chon_doi_tuong">
            <label>Đối tượng áp dụng</label>

            {{-- Dropdown sản phẩm --}}
            <select name="doi_tuong_id" id="chon_sanpham" class="loai_ap_dung"
                {{ $khuyenmai->loai_ap_dung == 'loai_san_pham' ? 'style=display:none' : '' }}>
                @foreach($sanPham as $sp)
                <option value="{{ $sp->id }}" {{ $sp->id == $khuyenmai->doi_tuong_id ? 'selected' : '' }}>
                    {{ $sp->ten_san_pham }}
                </option>
                @endforeach
            </select>

            {{-- Dropdown loại sản phẩm --}}
            <select name="doi_tuong_id" id="chon_loaisp" class="loai_ap_dung"
                {{ $khuyenmai->loai_ap_dung == 'san_pham' ? 'style=display:none' : '' }}>
                @foreach($loaiSanPham as $loai)
                <option value="{{ $loai->id }}" {{ $loai->id == $khuyenmai->doi_tuong_id ? 'selected' : '' }}>
                    {{ $loai->ten_loai }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="muc_giam">Mức giảm (%)</label>
            <input type="number" name="muc_giam" id="muc_giam" min="0" max="100"
                value="{{ old('muc_giam', $khuyenmai->muc_giam) }}" required>
        </div>

        <div class="form-group">
            <label for="ngay_bat_dau">Ngày bắt đầu</label>
            <input type="datetime-local" name="ngay_bat_dau" id="ngay_bat_dau"
                value="{{ old('ngay_bat_dau', \Carbon\Carbon::parse($khuyenmai->ngay_bat_dau)->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="form-group">
            <label for="ngay_ket_thuc">Ngày kết thúc</label>
            <input type="datetime-local" name="ngay_ket_thuc" id="ngay_ket_thuc"
                value="{{ old('ngay_ket_thuc', \Carbon\Carbon::parse($khuyenmai->ngay_ket_thuc)->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="btn-group">
            <a href="{{ route('khuyenmai.index') }}" class="btn btn-secondary">← Quay lại</a>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loaiApDung = document.getElementById('loai_ap_dung');
        const chonSanPham = document.getElementById('chon_sanpham');
        const chonLoaiSP = document.getElementById('chon_loaisp');

        function toggleDropdown() {
            if (loaiApDung.value === 'san_pham') {
                chonSanPham.style.display = 'block';
                chonLoaiSP.style.display = 'none';
                chonLoaiSP.name = '';
                chonSanPham.name = 'doi_tuong_id';
            } else {
                chonSanPham.style.display = 'none';
                chonLoaiSP.style.display = 'block';
                chonSanPham.name = '';
                chonLoaiSP.name = 'doi_tuong_id';
            }
        }

        loaiApDung.addEventListener('change', toggleDropdown);
    });
</script>
@endsection