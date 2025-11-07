@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/sanphamds.css') }}">
@section('title', 'Danh sách sản phẩm')


@section('full')
<div class="page-wrapper">
    {{-- Cột trái cố định --}}
    <div class="sidebar-left">
        <ul>
        </ul>
    </div>
    <div class="product-list-container">


        <form action="{{ route('sanpham.index') }}" method="GET" class="search-barsp">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm sản phẩm..."
                class="search-inputsp">
            @if(request('loai'))
            <input type="hidden" name="loai" value="{{ request('loai') }}">
            @endif
            <button type="submit" class="search-btnsp"><i class="bi bi-search"></i></button>
        </form>

        @if($loaiSanPhams->isNotEmpty())
        <div class="category-bar">
            <a href="{{ route('sanpham.index') }}" class="category-item all {{ request('loai') ? '' : 'active' }}">Tất cả</a>
            @foreach($loaiSanPhams as $loai)
            <a href="{{ route('sanpham.index', ['loai' => $loai->id]) }}"
                class="category-item {{ request('loai') == $loai->id ? 'active' : '' }}">
                {{ $loai->ten_loai }}
            </a>
            @endforeach
        </div>
        @endif
        <h2 class="page-title">Danh sách sản phẩm</h2>
        @if($sanPhams->isEmpty())
        <p class="empty-text">Chưa có sản phẩm nào được đăng.</p>
        @else
        <div class="product-grid">
            @foreach($sanPhams as $sp)
            @php
            // Giải mã mảng ảnh an toàn
            $images = json_decode($sp->hinh_anh, true);
            $firstImage = (is_array($images) && count($images) > 0)
            ? $images[0]
            : 'img/no-image.png';
            @endphp

            <div class="product-card">
                <a href="{{ route('sanpham.chitiet', $sp->id) }}" class="product-link">
                    {{-- Ảnh sản phẩm --}}
                    <div class="product-img">
                        <img
                            src="{{ asset('public/storage/' . $firstImage) }}"
                            onerror="this.onerror=null;this.src='{{ asset('public/storage/img/no-image.png') }}';"
                            alt="{{ $sp->ten_san_pham }}">
                    </div>

                    {{-- Thông tin sản phẩm --}}
                    <div class="product-info">
                        <h3>{{ $sp->ten_san_pham }}</h3>
                        @php
                        $avgRating = round($sp->danhGia->avg('so_sao'), 1);
                        @endphp

                        @if($sp->danhGia->count() > 0)
                        <p class="rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $avgRating ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                @endfor
                                <span>({{ $sp->danhGia->count() }})</span>

                        </p>
                        @else
                        <p class="rating-no">Chưa có đánh giá</p>
                        @endif
                        @if ($sp->gia_sau_khuyen_mai < $sp->gia)
                            <p class="price">
                                <span class="old-price" style="text-decoration: line-through; color: #888;">
                                    {{ number_format($sp->gia, 0, ',', '.') }}₫
                                </span>
                                <span class="new-price" style="color: #e53935; font-weight: bold; margin-left: 8px;">
                                    {{ number_format($sp->gia_sau_khuyen_mai, 0, ',', '.') }}₫
                                </span>
                                <span class="discount" style="color: #2e7d32; font-size: 0.9em; margin-left: 4px;">
                                    -{{ number_format(100 - ($sp->gia_sau_khuyen_mai / $sp->gia * 100), 0) }}%
                                </span>
                            </p>
                            @else
                            <p class="price">{{ number_format($sp->gia, 0, ',', '.') }}₫</p>
                            @endif
                            <p class="loai">{{ $sp->loaiSanPham->ten_loai ?? 'Không rõ loại' }}</p>
                            <p class="doanhnghiep">
                                Tên doanh nghiệp: {{ $sp->doanhNghiep->ten_cua_hang ?? 'Doanh nghiệp ẩn danh' }}
                            </p>
                            <p class="mota">{{ Str::limit($sp->mo_ta, 80, '...') }}</p>

                            @if($sp->so_luong == 0)
                            <p class="het-hang">Hết hàng</p>
                            @else
                            <p class="soluong">Số lượng: {{ $sp->so_luong }}</p>
                            @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        @endif
    </div>
</div>
@endsection