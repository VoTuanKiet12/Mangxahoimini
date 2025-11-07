@extends('admin.dashboard')
<link rel="stylesheet" href="{{ asset('public/css/adminloaisanpham.css') }}">
@section('title', 'Quản lý loại sản phẩm')

@section('quanly')
<div class="container-loai" data-aos="fade-down">
    <h2 class="title">Quản lý loại sản phẩm</h2>


    @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
    @endif


    <form action="{{ route('admin.loaisp.them') }}" method="POST" class="form-add">
        @csrf
        <input type="text" name="ten_loai" placeholder="Nhập tên loại sản phẩm..." required>
        <input type="text" name="mo_ta" placeholder="Mô tả (tuỳ chọn)">
        <button type="submit">Thêm loại</button>
    </form>


    <table class="loai-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên loại</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loaiSanPham as $loai)
            <tr>
                <td>{{ $loai->id }}</td>
                <td>
                    <form action="{{ route('admin.loaisp.sua', $loai->id) }}" method="POST" class="inline-form">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="ten_loai" value="{{ $loai->ten_loai }}" required>
                        <input type="text" name="mo_ta" value="{{ $loai->mo_ta }}" placeholder="Mô tả...">
                        <button type="submit" class="btn-update"> Lưu</button>
                    </form>
                </td>
                <td>{{ $loai->mo_ta ?? '—' }}</td>
                <td>
                    <form action="{{ route('admin.loaisp.xoa', $loai->id) }}" method="POST" onsubmit="return confirm('Xóa loại này?')" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty">Chưa có loại sản phẩm nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


@endsection