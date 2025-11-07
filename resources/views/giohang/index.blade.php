@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/giohangtc.css') }}">

@section('title', 'Giỏ hàng của tôi')


@section('full')

<div class="cart-container-page">
    @if($gioHang->isEmpty())
    <p class="empty-cart">Giỏ hàng trống.</p>
    @else
    <div>
        <table class="cart-table table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gioHang as $item)
                @php
                $images = json_decode($item->sanPham->hinh_anh, true);
                $firstImage = $images[0] ?? 'default.jpg';
                @endphp
                <tr data-id="{{ $item->id }}">
                    <td>
                        <img src="{{ asset('public/storage/' . ltrim($firstImage, '/')) }}"
                            alt="{{ $item->sanPham->ten_san_pham }}"
                            width="70" height="70"
                            style="object-fit:cover; border-radius:8px;">
                    </td>
                    <td class="text-start">{{ $item->sanPham->ten_san_pham }}</td>
                    <td>
                        @php
                        $km = $item->sanPham->khuyenMaiHienTai()->first();
                        $giaSauGiam = $km ? $item->sanPham->gia_sau_khuyen_mai : $item->sanPham->gia;
                        @endphp

                        @if($km)
                        <div class="gia-container">
                            <span class="gia-sau-giam">{{ number_format($giaSauGiam, 0, ',', '.') }}₫</span>
                            <small class="khuyen-mai">Giảm {{ $km->muc_giam }}%</small>
                        </div>
                        @else
                        <div class="gia-container">
                            <span class="gia-goc">{{ number_format($item->sanPham->gia, 0, ',', '.') }}₫</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="quantity-control" data-id="{{ $item->id }}">
                            <button type="button" class="btn-qty decrease">−</button>
                            <span class="qty">{{ $item->so_luong }}</span>
                            <button type="button" class="btn-qty increase">+</button>
                        </div>
                    </td>
                    <td class="tong">
                        @php
                        $km = $item->sanPham->khuyenMaiHienTai()->first();
                        $giaSauGiam = $km ? $item->sanPham->gia_sau_khuyen_mai : $item->sanPham->gia;
                        $tong = $item->so_luong * $giaSauGiam;
                        @endphp

                        {{ number_format($tong, 0, ',', '.') }}₫
                    </td>

                    <td>
                        <form action="{{ route('giohang.xoa', $item->id) }}" method="POST"
                            onsubmit="return confirm('Xóa sản phẩm này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete">✕</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
    @endif

    @if(!$gioHang->isEmpty())
    <div class="cart-summary">
        <h3>Tóm tắt đơn hàng</h3>

        <div class="summary-item">
            <span>Tổng tiền ({{ count($gioHang) }} sản phẩm):</span>
            <span>{{ number_format($tongGoc, 0, ',', '.') }}₫</span>
        </div>

        <div class="summary-item discount">
            <span>Giảm giá:</span>
            <span>-{{ number_format($tongGiam, 0, ',', '.') }}₫</span>
        </div>

        <div class="summary-item">
            <span>Thuế VAT (10%):</span>
            <span id="vat">{{ number_format($vat ?? 0, 0, ',', '.') }}₫</span>
        </div>

        <div class="summary-item">
            <span>Phí vận chuyển:</span>
            <span>Tính khi thanh toán</span>
        </div>

        <div class="summary-item total">
            <span>Tổng cộng (đã gồm VAT):</span>
            <span id="tongTatCa2">{{ number_format($tongCuoi, 0, ',', '.') }}₫</span>
        </div>

        <button type="button" class="btn-checkout" onclick="thanhToanGioHang()">Tiến hành thanh toán</button>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrf = "{{ csrf_token() }}";


        document.querySelectorAll('.quantity-control').forEach(control => {
            const id = control.dataset.id;
            const dec = control.querySelector('.decrease');
            const inc = control.querySelector('.increase');
            const qtySpan = control.querySelector('.qty');

            if (dec) dec.addEventListener('click', () => updateQuantity(id, 'giam', qtySpan));
            if (inc) inc.addEventListener('click', () => updateQuantity(id, 'tang', qtySpan));
        });


        function updateQuantity(id, action, qtySpan) {
            const url = action === 'tang' ?
                "{{ route('giohang.tang', ':id') }}".replace(':id', id) :
                "{{ route('giohang.giam', ':id') }}".replace(':id', id);

            fetch(url, {
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        "Accept": "application/json"
                    }
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch {
                        console.error("Phản hồi không phải JSON:", text);
                        throw new Error("Phản hồi không hợp lệ từ server.");
                    }
                })
                .then(data => {
                    if (data.success) {
                        const row = qtySpan.closest('tr');
                        const tongItem = row.querySelector('.tong');
                        qtySpan.textContent = data.so_luong;
                        if (tongItem) tongItem.textContent = data.tong;
                        const tongTatCa = document.getElementById('tongTatCa2');
                        if (tongTatCa && data.tong_tat_ca)
                            tongTatCa.textContent = data.tong_tat_ca;
                        const tongGocSpan = document.querySelector('.summary-item.goc span:last-child');
                        const giamGiaSpan = document.querySelector('.summary-item.discount span:last-child');
                        const vatSpan = document.getElementById('vat');

                        if (data.tong_goc !== undefined && tongGocSpan)
                            tongGocSpan.textContent = new Intl.NumberFormat('vi-VN').format(data.tong_goc) + "₫";
                        if (data.tong_giam !== undefined && giamGiaSpan)
                            giamGiaSpan.textContent = "-" + new Intl.NumberFormat('vi-VN').format(data.tong_giam) + "₫";
                        if (data.vat !== undefined && vatSpan)
                            vatSpan.textContent = new Intl.NumberFormat('vi-VN').format(data.vat) + "₫";
                    } else if (data.deleted) {
                        qtySpan.closest('tr').remove();
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            document.querySelector('.cart-container-page').innerHTML = '<p class="empty-cart">Giỏ hàng trống.</p>';
                        }
                    } else if (data.message) {
                        alert(data.message);
                    } else {
                        alert(data.error || "Có lỗi xảy ra!");
                    }
                })
                .catch(err => console.error("❌ Lỗi cập nhật:", err));
        }
    });
</script>
<script>
    function thanhToanGioHang() {
        window.location.href = "{{ route('donhang.thanhtoanGioHang') }}";
    }
</script>
@endsection