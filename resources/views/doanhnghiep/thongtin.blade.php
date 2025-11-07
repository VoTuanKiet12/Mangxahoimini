@extends('doanhnghiep.quanly')
<link rel="stylesheet" href="{{ asset('public/css/doanhnghiepthongtin.css') }}">
@section('quanly')


<div class="info-grid">
    {{-- Box 1: Thông tin cửa hàng --}}
    <div class="info-box" data-aos="fade-right">
        <h3>Thông tin cửa hàng</h3>
        <div>
            <p><strong>Tên cửa hàng:</strong> {{ $doanhNghiep->ten_cua_hang }}</p>
            <p><strong>Mô tả:</strong> {{ $doanhNghiep->mo_ta ?? 'Chưa có mô tả' }}</p>
            <p><strong>Địa chỉ:</strong> {{ $doanhNghiep->dia_chi ?? 'Chưa cập nhật' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $doanhNghiep->so_dien_thoai ?? 'Chưa cập nhật' }}</p>
            <p><strong>Trạng thái:</strong>
                <span class="status {{ $doanhNghiep->trang_thai }}">
                    {{ ucfirst(str_replace('_', ' ', $doanhNghiep->trang_thai)) }}
                </span>
            </p>
        </div>
        <a href="{{ route('doanhnghiep.edit', $doanhNghiep->id) }}" class="edit-btn">
            Chỉnh sửa thông tin
        </a>
    </div>

    {{-- Box 2: Logo --}}
    <div class="info-box" data-aos="fade-left">
        <h3>Logo cửa hàng</h3>
        <div class="info-logo-dn">
            <img src="{{ asset('public/storage/' . $doanhNghiep->logo) }}" alt="Logo cửa hàng">
        </div>
    </div>

    {{-- Box 3: Liên hệ --}}
    <div class="info-box full " data-aos="fade-up">
        <h3>Liên hệ</h3>
        <div>
            <p><strong>Địa chỉ:</strong> {{ $doanhNghiep->dia_chi ?? 'Chưa cập nhật' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $doanhNghiep->so_dien_thoai ?? 'Chưa cập nhật' }}</p>
        </div>
    </div>
</div>
@endsection