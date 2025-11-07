@extends('layouts.app')
@section('title', 'Xác nhận đơn hàng')

@section('content')
<div class="order-confirm">
    <h3>✅ Đặt hàng thành công!</h3>
    <p>Mã đơn: #{{ $donHang->id }}</p>
    <p>Sản phẩm: {{ $donHang->chiTiet->first()->sanPham->ten_san_pham }}</p>
    <p>Tổng tiền: {{ number_format($donHang->tong_tien, 0, ',', '.') }}₫</p>
    <a href="{{ route('donhang.index') }}" class="btn btn-primary">Xem tất cả đơn hàng</a>
</div>
@endsection