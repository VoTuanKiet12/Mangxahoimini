@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/donhangdamua.css') }}">
@section('full')



<div class="container">
    <h3>Đơn hàng của bạn</h3>

    @if($donHangs->isEmpty())
    <div class="alert">Bạn chưa có đơn hàng nào.</div>
    @else
    @foreach($donHangs as $donHang)
    <div class="card">
        <div class="card-header">
            <span>Đơn hàng #{{ $donHang->id }}</span> —
            <span class="badge">{{ ucfirst($donHang->trang_thai) }}</span>
            <span class="float-end">{{ $donHang->created_at->format('d/m/Y H:i') }}</span>
            <div style="clear: both;"></div>
        </div>

        <div class="card-body">
            @foreach($donHang->chiTietDonHang as $ct)
            <div class="order-item">
                @php
                $images = json_decode($ct->sanPham->hinh_anh, true);
                $firstImage = is_array($images) && count($images) > 0
                ? $images[0]
                : 'img/no-image.png';
                @endphp
                <img src="{{ asset('public/storage/' . $firstImage) }}" alt="{{ $ct->sanPham->ten_san_pham }}">
                <div>
                    <h6>{{ $ct->sanPham->ten_san_pham }}</h6>
                    <p class="chu">Số lượng: {{ $ct->so_luong }}</p>
                    <p class="text-danger"><strong>{{ number_format($ct->don_gia, 0, ',', '.') }}₫</strong></p>
                </div>
            </div>
            @endforeach

            <div>
                <p class="chu"><strong>Tổng tiền:</strong> {{ number_format($donHang->tong_tien, 0, ',', '.') }}₫</p>
                <p class="chu"><strong>Phương thức thanh toán:</strong> {{ ucfirst($donHang->thanhToan->phuong_thuc ?? 'N/A') }}</p>
            </div>


            @if(in_array($donHang->trang_thai, ['cho_xac_nhan', 'da_huy']))
            <form action="{{ route('donhang.xoa', $donHang->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này không?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete-order">🗑 Xóa đơn hàng</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
    @endif
</div>

@endsection