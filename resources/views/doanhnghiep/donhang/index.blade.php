@extends('doanhnghiep.quanly')
<link rel="stylesheet" href="{{ asset('public/css/quanlydonhangdn.css') }}">

@section('quanly')
<div class="order-management" data-aos="zoom-in">
    <h2 class="page-title">Quản lý danh sách đơn hàng</h2>

    <table class="order-table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Người nhận</th>
                <th>Số điện thoại</th>
                <th>Sản phẩm</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>

            </tr>
        </thead>
        <tbody>
            @foreach($donHangs as $donHang)
            <tr>
                <td class="hang">{{ $donHang->id }}</td>
                <td class="hang">{{ $donHang->ten_nguoi_nhan }}</td>
                <td class="hang">{{ $donHang->so_dien_thoai }}</td>

                <td class="hang">
                    @foreach($donHang->chiTietDonHang as $ct)
                    <div class="product-item">{{ $ct->sanPham->ten_san_pham }} <span>(x{{ $ct->so_luong }})</span></div>
                    @endforeach
                </td>

                <td class="price">{{ number_format($donHang->tong_tien, 0, ',', '.') }}₫</td>

                <td class="hang">
                    <form action="{{ route('doanhnghiep.donhang.update', $donHang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="trang_thai" onchange="this.form.submit()" class="status-select {{ $donHang->trang_thai }}">
                            @foreach(['cho_xac_nhan'=>'Chờ xác nhận','dang_giao'=>'Đang giao','hoan_thanh'=>'Hoàn thành','huy'=>'Hủy'] as $key=>$label)
                            <option value="{{ $key }}" {{ $donHang->trang_thai==$key?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>

                <td class="hang">{{ $donHang->created_at ? $donHang->created_at->format('d/m/Y') : '' }}</td>


            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection