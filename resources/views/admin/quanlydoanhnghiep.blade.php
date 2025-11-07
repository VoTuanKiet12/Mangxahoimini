@extends('admin.dashboard')

@section('title', 'Quản lý doanh nghiệp')
<link rel="stylesheet" href="{{ asset('public/css/admindoanhnghiep.css') }}">
@section('quanly')


<div class="dashboard-container" data-aos="fade-down">
    <h2>Quản lý đăng ký doanh nghiệp</h2>



    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h4 style="color:#28a745;"> Doanh nghiệp chờ duyệt</h4>
    <table>
        <thead>
            <tr>
                <th>Tên cửa hàng</th>
                <th>Chủ sở hữu</th>
                <th>Địa chỉ</th>
                <th>Số điện thoại</th>
                <th>Logo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($choDuyet as $dn)
            <tr>
                <td>{{ $dn->ten_cua_hang }}</td>
                <td>{{ $dn->user->name ?? 'N/A' }}</td>
                <td>{{ $dn->dia_chi }}</td>
                <td>{{ $dn->so_dien_thoai }}</td>
                <td>
                    @if($dn->logo)
                    <img src="{{ asset('public/storage/' . $dn->logo) }}">
                    @else
                    <span class="text-muted">Không có</span>
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.doanhnghiep.duyet', $dn->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-success">Duyệt</button>
                    </form>
                    <form action="{{ route('admin.doanhnghiep.tuchoi', $dn->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger">Từ chối</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-muted">Không có doanh nghiệp chờ duyệt.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


</div>
@endsection