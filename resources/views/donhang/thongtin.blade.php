@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/muahang.css') }}">
@section('title', 'Thanh toán đơn hàng')

@section('full')
<div class="checkout-container">

    {{-- ====== CỘT TRÁI: THÔNG TIN GIAO HÀNG ====== --}}
    <div class="checkout-left">


        <h1><span class="so">1</span> Thông tin giao hàng</h1>

        {{-- Hiển thị thông báo lỗi --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('donhang.store') }}" method="POST">
            @csrf
            <input type="hidden" name="san_pham_id" value="{{ $sanPham->id }}">

            <div class="form-group">
                <label for="ten_nguoi_nhan">Họ và tên *</label>
                <input type="text" name="ten_nguoi_nhan" id="ten_nguoi_nhan"
                    class="form-control" value="{{ old('ten_nguoi_nhan', Auth::user()->name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="so_dien_thoai">Điện thoại giao hàng *</label>
                <input type="text" name="so_dien_thoai" id="so_dien_thoai"
                    class="form-control" value="{{ old('so_dien_thoai', Auth::user()->so_dien_thoai ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="email_nguoi_nhan">Email *</label>
                <input type="email" name="email_nguoi_nhan" id="email_nguoi_nhan"
                    class="form-control" value="{{ old('email_nguoi_nhan', Auth::user()->email ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="dia_chi_giao">Địa chỉ giao hàng *</label>
                <input type="text" name="dia_chi_giao" id="dia_chi_giao"
                    class="form-control" value="{{ old('dia_chi_giao', Auth::user()->dia_chi ?? '') }}" required>
            </div>

            <h1><span class="so2">2</span> Phương thức thnh tón</h1>

            <div class="form-group">
                <label>Chọn phương thức thanh toán:</label>
                <div class="payment-methods">
                    <label><input type="radio" name="phuong_thuc" value="tien_mat" checked> Thanh toán khi nhận hàng</label>
                    <label><input type="radio" name="phuong_thuc" value="chuyen_khoan"> Thẻ tín dụng / ghi nợ</label>
                    <label><input type="radio" name="phuong_thuc" value="vi_dien_tu"> Ví điện tử</label>
                </div>
            </div>

            <div class="form-group agree">
                <label>
                    <input type="checkbox" required>
                    Tôi đồng ý với các <a href="#">Điều khoản và Điều kiện</a>
                </label>
            </div>

            <button type="submit" class="btn">Xác nhận thanh toán</button>
        </form>
    </div>

    {{-- ====== CỘT PHẢI: TÓM TẮT ĐƠN HÀNG ====== --}}
    <div class="order-summary">
        <h3>Tóm tắt đơn hàng</h3>
        <div class="summary-images">
            @foreach(json_decode($sanPham->hinh_anh, true) as $img)
            <img src="{{ asset('public/storage/' . $img) }}" alt="">
            @endforeach
        </div>

        @php
        $giaThucTe = $sanPham->gia_sau_khuyen_mai < $sanPham->gia
            ? $sanPham->gia_sau_khuyen_mai
            : $sanPham->gia;
            $tongTien = $giaThucTe * $soLuong;
            $vat = $tongTien * 0.10;
            $tongCong = $tongTien + $vat;
            @endphp

            <div class="order-detail">
                <div class="order-line">
                    <span>Tổng tiền ({{ $soLuong }} sản phẩm):</span>
                    <span>{{ number_format($tongTien, 0, ',', '.') }}₫</span>
                </div>

                @if ($sanPham->gia_sau_khuyen_mai < $sanPham->gia)
                    <div class="order-line discount">
                        <span>Giảm giá:</span>
                        <span>-{{ number_format($sanPham->gia - $sanPham->gia_sau_khuyen_mai, 0, ',', '.') }}₫</span>
                    </div>
                    @endif

                    <div class="order-line">
                        <span>Thuế VAT (10%):</span>
                        <span class="vat">{{ number_format($vat, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="order-line">
                        <span>Phí vận chuyển:</span>
                        <span class="pvc">0₫</span>
                    </div>

                    <hr>

                    <div class="order-line total">
                        <strong>Tổng ước tính:</strong>
                        <strong class="tong">{{ number_format($tongCong, 0, ',', '.') }}₫</strong>
                    </div>
            </div>
    </div>

</div>
@endsection