@extends('admin.dashboard')

@section('title', 'Quản lý hệ thống')
<link rel="stylesheet" href="{{ asset('public/css/adminnguoidung.css') }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@section('quanly')
<div class="dashboard-container">
    <h2 class="dashboard-title">Bảng điều khiển quản trị</h2>

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

    <h3 class="dashboard-subtitle">Người dùng gần đây</h3>

    <table class="dashboard-table" data-aos="fade-right">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name ?? $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>


                    <form action="{{ route('admin.updateRole', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <select name="role" class="form-select form-select-sm d-inline w-auto"
                            onchange="this.form.submit()" style="display:inline-block; width:110px;">
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>

                </td>
                <td>
                    <span class="status {{ $user->trang_thai === 'hoat_dong' ? 'active' : 'inactive' }}">
                        {{ $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Vô hiệu hóa' }}
                    </span>
                </td>
                <td>{{ $user->created_at->diffForHumans() }}</td>
                <td>
                    @if(Auth::id() !== $user->id)
                    <form action="{{ route('admin.toggleUser', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="btn-status {{ $user->trang_thai === 'hoat_dong' ? 'btn-disable' : 'btn-enable' }}">
                            @if($user->trang_thai === 'hoat_dong')
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
        {{ $users->links() }}
    </div>
</div>

<script src="{{ asset('public/js/hieuungso.js') }}"></script>


@endsection