@extends('doanhnghiep.quanly')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="{{ asset('public/vendor/font-awesome/css/all.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/css/doanhnghiepbanchay.css') }}">
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@section('quanly')
<div class="top-products-container" data-aos="fade-up">
    <h3> Sản phẩm bán chạy nhất</h3>

    <div class="products-grid">
        @foreach($topSanPham as $sp)
        @php
        // Giải mã JSON an toàn
        $images = json_decode($sp->hinh_anh, true);
        // Lấy ảnh đầu tiên hoặc ảnh mặc định
        $firstImage = (is_array($images) && count($images) > 0)
        ? $images[0]
        : 'img/no-image.png';
        @endphp

        <div class="product-card" data-aos="fade-up">
            <img
                class="product-img"
                src="{{ asset('public/storage/' . $firstImage) }}" onerror="this.onerror=null;this.src='{{ asset('public/storage/img/no-image.png') }}';"
                alt="{{ $sp->ten_san_pham }}">

            <div class="product-info">
                <h6>{{ $sp->ten_san_pham }}</h6>
                <p>Đã bán: <strong>{{ $sp->tong_ban }}</strong></p>
                <a href="{{ route('sanpham.chitiet', $sp->id) }}" class="btn-view">
                    Xem chi tiết
                </a>
            </div>
        </div>
        @endforeach

    </div>

</div>


@endsection