@extends('doanhnghiep.quanly')

@section('quanly')
<link rel="stylesheet" href="{{ asset('public/css/khuyenmaiql.css') }}">

<div class="container-khuyenmai">
    <h3>Danh sách khuyến mãi</h3>

    <a href="{{ route('khuyenmai.create') }}" class="btn-primary">+ Thêm khuyến mãi</a>

    @if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif

    <table class="table-khuyenmai">
        <thead>
            <tr>
                <th>Tên khuyến mãi</th>
                <th>Áp dụng</th>
                <th>Mức giảm (%)</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Trạng thái</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($khuyenMais as $km)
            <tr>
                <td>{{ $km->ten_khuyen_mai }}</td>
                <td>{{ ucfirst($km->loai_ap_dung) }}</td>
                <td>{{ $km->muc_giam }}%</td>
                <td>{{ $km->ngay_bat_dau }}</td>
                <td>{{ $km->ngay_ket_thuc }}</td>
                <td>
                    <span class="badge {{ $km->trang_thai == 'hoat_dong' ? 'badge-active' : 'badge-expired' }}">
                        {{ ucfirst($km->trang_thai) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('khuyenmai.edit', $km->id) }}" class="btn-edit">Sửa</a>

                </td>
                <td>
                    <form action="{{ route('khuyenmai.destroy', $km->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn-delete" onclick="return confirm('Xóa khuyến mãi này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="empty">Chưa có khuyến mãi nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection