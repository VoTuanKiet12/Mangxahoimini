@extends('admin.dashboard')

@section('title', 'Quản lý doanh nghiệp')
<link rel="stylesheet" href="{{ asset('public/css/admin-dsdoanhnghiep.css') }}">

@section('quanly')
<div class="dashboard-container">
    <h2 class="dashboard-title">Danh sách doanh nghiệp</h2>

    <div class="dashboard-stats">
        <a href="{{ route('admin.baiviet') }}">
            <div class="dashboard-card" data-aos="fade-right">

                <strong>{{ $tongBaiViet }}</strong>
                <span>Tổng bài viết</span>
            </div>
        </a>
        <a href="{{ route('admin.nguoidung') }}">
            <div class="dashboard-card" data-aos="fade-up">
                <strong>{{ $tongUser }}</strong>
                <span>Tổng người đăng</span>
            </div>
        </a>
        <a href="{{ route('admin.doanhnghiep.index') }}">
            <div class="dashboard-card" data-aos="fade-right">
                <strong>{{ $tongDoanhNghiep }}</strong>
                <span>Doanh nghiệp</span>
            </div>
        </a>
    </div>

    <h3 class="dashboard-subtitle">Tài khoản doanh nghiệp</h3>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên doanh nghiệp</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doanhNghiep as $dn)
            <tr>
                <td>{{ $dn->id }}</td>
                <td>{{ $dn->name ?? $dn->username }}</td>
                <td>{{ $dn->email }}</td>
                <td>
                    <span class="status {{ $dn->trang_thai === 'hoat_dong' ? 'active' : 'inactive' }}">
                        {{ $dn->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Vô hiệu hóa' }}
                    </span>
                </td>
                <td>{{ $dn->created_at->diffForHumans() }}</td>
                <td>
                    @if(Auth::id() !== $dn->id)
                    <form action="{{ route('admin.toggleUser', $dn->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="btn-status {{ $dn->trang_thai === 'hoat_dong' ? 'btn-disable' : 'btn-enable' }}">
                            @if($dn->trang_thai === 'hoat_dong')
                            <i class="bi bi-lock-fill"></i>
                            @else
                            <i class="bi bi-unlock-fill"></i>
                            @endif
                        </button>
                    </form>
                    @else
                    <em>Không thể thao tác</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $doanhNghiep->links() }}
    </div>
</div>
<script src="{{ asset('public/js/hieuungso.js') }}"></script>

@endsection