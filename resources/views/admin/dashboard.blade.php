<link rel="stylesheet" href="{{ asset('public/css/admin.css') }}">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<nav class="navbar" data-aos="fade-down">
    <div class="nav-left">
        <a class="navbar-logo">Mạng XH</a>
    </div>
    <div class="nav-center">
        <p>Trang admin</p>

    </div>
    <div class="navbar-user">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="color:red;" class="logout-btn"><i class="bi bi-door-closed-fill"></i> </button>
        </form>

    </div>
</nav>
<div class="admin-container">

    <div class="admin-sidebar">
        <h3>Bảng Điều Khiển</h3>
        <div class="admin-menu">
            <a href="{{ route('admin.nguoidung') }}"
                class="admin-link {{ request()->routeIs('admin.nguoidung') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> Quản lý người dùng
            </a>
            <a href="{{ route('admin.baiviet') }}"
                class="admin-link {{ request()->routeIs('admin.baiviet') ? 'active' : '' }}">
                <i class="bi bi-book"></i> Quản lý bài viết
            </a>
            <a href="{{ route('admin.doanhnghiep.list') }}"
                class="admin-link {{ request()->routeIs('admin.doanhnghiep.list') ? 'active' : '' }}">
                <i class="bi bi-box2-fill"></i> Quản lý danh sách doanh nghiêp
            </a>
            <a href="{{ route('admin.doanhnghiep.index') }}"
                class="admin-link {{ request()->routeIs('admin.doanhnghiep.index') ? 'active' : '' }}">
                <i class="bi bi-building-fill"></i> Quản lý đăng ký doanh nghiệp
            </a>
            <a href="{{ route('admin.loaisp.danhsach') }}"
                class="admin-link {{ request()->routeIs('admin.loaisp.danhsach') ? 'active' : '' }}">
                <i class="bi bi-box2-fill"></i> Quản lý loại sản phẩm
            </a>

            </a>
        </div>
    </div>


    <div class="admin-content">
        @hasSection('quanly')
        @yield('quanly')
        @else
        @include('admin.doanhnghiep.index')
        @endif
    </div>
</div>

<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>