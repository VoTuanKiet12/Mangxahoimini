@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/doanhnghiepquanly.css') }}">
@section('full')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>


<div class="business-container">
    {{-- Sidebar trái --}}
    <div class="business-links">
        <h3><i class="bi bi-clipboard-data-fill"></i> Quản Lý</h3>
        <div class="business-links1">
            <a href="{{ route('doanhnghiep.thongtin') }}"
                class="secondary {{ request()->routeIs('doanhnghiep.thongtin') ? 'active' : '' }}">
                <i class="bi bi-info-circle-fill"></i> Thông tin cửa hàng
            </a>

            <a href="{{ route('doanhnghiep.donhang.index') }}"
                class="secondary {{ request()->routeIs('doanhnghiep.donhang.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Quản lý đơn hàng
            </a>

            <a href="{{ route('doanhnghiep.sanpham.index') }}"
                class="secondary {{ request()->routeIs('doanhnghiep.sanpham.index') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Cửa hàng của bạn
            </a>

            <a href="{{ route('doanhnghiep.thongke') }}"
                class="secondary {{ request()->routeIs('doanhnghiep.thongke') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Thống kê doanh thu
            </a>
            <a href="{{ route('doanhnghiep.sanpham.top_ban_chay') }}"
                class="secondary {{ request()->routeIs('doanhnghiep.sanpham.top_ban_chay') ? 'active' : '' }}">
                <i class="bi bi-fire"></i> Top sản phẩm bán chạy
            </a>
            <a href="{{ route('khuyenmai.index') }}"
                class="secondary {{ request()->routeIs('khuyenmai.*') ? 'active' : '' }}">
                <i class="bi bi-gift-fill"></i> Quản lý khuyến mãi
            </a>
            <a href="{{ route('doanhnghiep.baiviet.create') }}"
                class="secondary {{ request()->routeIs('doanhnghiep.baiviet.create') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i> Đăng bài viết
            </a>


        </div>
    </div>

    <div class="business-content">
        @hasSection('quanly')
        @yield('quanly')
        @else
        @include('doanhnghiep.thongtin')
        @endif
    </div>


</div>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>

@endsection