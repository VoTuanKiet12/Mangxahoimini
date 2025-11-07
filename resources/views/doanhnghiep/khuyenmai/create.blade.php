@extends('doanhnghiep.quanly')

@section('quanly')
<link rel="stylesheet" href="{{ asset('public/css/khuyenmaiql.css') }}">

<div class="container-khuyenmai">
    <h3>Thêm khuyến mãi</h3>

    <form method="POST" action="{{ route('khuyenmai.store') }}" class="form-khuyenmai">
        @csrf

        {{-- ===== TÊN KHUYẾN MÃI ===== --}}
        <div class="form-group">
            <label for="ten_khuyen_mai">Tên khuyến mãi</label>
            <input type="text" id="ten_khuyen_mai" name="ten_khuyen_mai" required>
        </div>

        {{-- ===== LOẠI ÁP DỤNG ===== --}}
        <div class="form-group">
            <label for="loai_ap_dung">Áp dụng cho</label>
            <select id="loai_ap_dung" name="loai_ap_dung" required>
                <option value="san_pham">Sản phẩm</option>
                <option value="loai_san_pham">Loại sản phẩm</option>
            </select>
        </div>

        {{-- ===== ĐỐI TƯỢNG ÁP DỤNG ===== --}}
        <div class="form-group">
            <label for="doi_tuong_id">Đối tượng áp dụng</label>

            {{-- Hiển thị danh sách sản phẩm --}}
            <select id="doi_tuong_sanpham" name="doi_tuong_sanpham_id">
                <option value="">-- Chọn sản phẩm --</option>
                @foreach($sanPham as $sp)
                <option value="{{ $sp->id }}">{{ $sp->ten_san_pham }}</option>
                @endforeach
            </select>

            {{-- Hiển thị danh sách loại sản phẩm (ẩn mặc định) --}}
            <select id="doi_tuong_loai" name="doi_tuong_loai_id" style="display: none;">
                <option value="">-- Chọn loại sản phẩm --</option>
                @foreach($loaiSanPham as $loai)
                <option value="{{ $loai->id }}">{{ $loai->ten_loai }}</option>
                @endforeach
            </select>
        </div>

        {{-- ===== MỨC GIẢM ===== --}}
        <div class="form-group">
            <label for="muc_giam">Mức giảm (%)</label>
            <input type="number" id="muc_giam" name="muc_giam" step="0.01" max="100" required>
        </div>

        {{-- ===== NGÀY BẮT ĐẦU / KẾT THÚC ===== --}}
        <div class="form-row">
            <div class="form-group half">
                <label for="ngay_bat_dau">Ngày bắt đầu</label>
                <input type="datetime-local" id="ngay_bat_dau" name="ngay_bat_dau" required>
            </div>
            <div class="form-group half">
                <label for="ngay_ket_thuc">Ngày kết thúc</label>
                <input type="datetime-local" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">Lưu khuyến mãi</button>
            <a href="{{ route('khuyenmai.index') }}" class="btn-back">← Quay lại</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loaiApDung = document.getElementById('loai_ap_dung');
        const selectSanPham = document.getElementById('doi_tuong_sanpham');
        const selectLoai = document.getElementById('doi_tuong_loai');

        // Khi thay đổi "Áp dụng cho"
        loaiApDung.addEventListener('change', function() {
            if (this.value === 'loai_san_pham') {
                selectSanPham.style.display = 'none';
                selectLoai.style.display = 'block';
            } else {
                selectSanPham.style.display = 'block';
                selectLoai.style.display = 'none';
            }
        });
    });
</script>
@endsection