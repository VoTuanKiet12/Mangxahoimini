<link rel="stylesheet" href="{{ asset('public/css/nav.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


@php
use App\Models\ThongBao;
use Illuminate\Support\Facades\Auth;

$thongBaos = Auth::check()
? ThongBao::where('user_id', Auth::id())->orderByDesc('id')->take(15)->get()
: collect();

$coThongBaoMoi = $thongBaos->where('da_doc', 0)->count() > 0;
@endphp

<nav class="navbar" data-aos="fade-down">
    <div class="nav-left">
        <a href="/MangXaHoiMiNi/trangchu" class="navbar-logo">Mạng XH</a>
        <div class="search-container">
            <a href="javascript:void(0)"
                title="Tìm kiếm"
                class="icon-link"
                onclick="toggleSearch()">
                <i class="bi bi-search"></i>
            </a>

            {{-- Ô tìm kiếm (ẩn/hiện khi click) --}}
            <form action="{{ route('timkiem') }}" method="GET">
                <input type="text" id="searchInput" name="q" class="search-input" placeholder="Tìm kiếm người dùng...">
            </form>
        </div>

    </div>
    <div class="nav-center">
        <a href="{{ url('/trangchu') }}"
            title="Trang chủ"
            class="icon-link {{ request()->is('MangXaHoiMiNi/trangchu') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
        </a>


        <a href="{{ route('nhom.index') }}"
            title="Nhóm"
            class="icon-link {{ request()->routeIs('nhom.index') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
        </a>

        <a href="{{ route('bai-viet.video') }}"
            title="Video"
            class="icon-link {{ request()->routeIs('bai-viet.video') ? 'active' : '' }}">
            <i class="bi bi-play-btn-fill"></i>
        </a>

        <a href="dam_th.php"
            title="Trò chơi"
            class="icon-link <?= basename($_SERVER['PHP_SELF']) == 'dam_thoai.php' ? 'active' : '' ?>">
            <i class="bi bi-controller"></i>
        </a>
    </div>

    @auth
    <div class="navbar-user">

        <div class="notification-container">
            <div class="notification-icon" onclick="toggleThongBao()">
                <i class="bi bi-bell-fill"></i>
                <span class="dot" id="notifyDot" style="{{ $coThongBaoMoi ? '' : 'display:none' }}"></span>
            </div>

            <div id="notification-box" class="notification-box">
                <h4>Thông báo</h4>

                @forelse($thongBaos as $tb)
                @if($tb->link)
                {{-- Nếu có link, bấm vào sẽ đi tới bài viết/trang liên quan --}}
                <a href="{{ $tb->link }}"
                    class="noti-item {{ $tb->da_doc ? '' : 'chua-doc' }}"
                    onclick="danhDauDaDoc({{ $tb->id }})">
                    <p class="linktb">{{ $tb->noi_dung }}</p>
                    <small>{{ $tb->created_at->diffForHumans() }}</small>
                </a>
                @else
                {{-- Nếu không có link, chỉ là thông báo văn bản --}}
                <div class="noti-item {{ $tb->da_doc ? '' : 'chua-doc' }}"
                    onclick="danhDauDaDoc({{ $tb->id }})">
                    <p>{{ $tb->noi_dung }}</p>
                    <small>{{ $tb->created_at->diffForHumans() }}</small>
                </div>
                @endif
                @empty
                <p class="empty">Không có thông báo nào.</p>
                @endforelse
            </div>
        </div>
        @php
        $gioHangMini = \App\Models\GioHang::where('user_id', Auth::id())
        ->with('sanPham')
        ->latest('updated_at')
        ->get();
        $tongSoLuongGioHang = $gioHangMini->sum('so_luong');
        @endphp
        {{-- === Giỏ hàng (mini cart) === --}}
        <div class="cart-container">
            <div class="cart-icon" id="cartIcon" onclick="toggleCart()">
                <i class="bi bi-cart3"></i>
                @if($tongSoLuongGioHang > 0)
                <span class="dot" id="cartDotgh">{{ $tongSoLuongGioHang }}</span>
                @endif
            </div>
            <div id="cart-box" class="cart-box">
                <h4>Giỏ hàng</h4>


                @forelse($gioHangMini as $item)
                <div class="cart-item" onclick="goToProduct('{{ route('sanpham.chitiet', $item->sanPham->id) }}')">
                    @php
                    $images = is_array($item->sanPham->hinh_anh)
                    ? $item->sanPham->hinh_anh
                    : json_decode($item->sanPham->hinh_anh, true);
                    $firstImage = $images[0] ?? 'default.png';
                    @endphp

                    <img src="{{ asset('public/storage/' . $firstImage) }}" alt="Ảnh sản phẩm">
                    <div class="cart-info">
                        <p>{{ $item->sanPham->ten_san_pham }}</p>
                        <div class="quantity-control" data-id="{{ $item->id }}" onclick="event.stopPropagation()">
                            <form action="{{ route('giohang.giam', $item->id) }}" method="POST" class="inline-form" onsubmit="return updateQuantity(event, this)">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="qty-btn">−</button>
                            </form>

                            <span class="qty">{{ $item->so_luong }}</span>

                            <form action="{{ route('giohang.tang', $item->id) }}" method="POST" class="inline-form" onsubmit="return updateQuantity(event, this)">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="qty-btn">+</button>
                            </form>
                        </div>

                        <small class="item-total">{{ number_format($item->sanPham->gia * $item->so_luong, 0, ',', '.') }}₫</small>
                    </div>

                    {{-- Nút xóa --}}
                    <form action="{{ route('giohang.xoa', $item->id) }}"
                        method="POST"
                        class="cart-delete-form"
                        onsubmit="event.stopPropagation(); return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-cart-delete" title="Xóa" onclick="event.stopPropagation();">
                            <i class="bi bi-x"></i>
                        </button>
                    </form>
                </div>
                @empty
                <p class="empty">Giỏ hàng trống.</p>
                @endforelse
                <div class="cart-footer">
                    <a href="{{ route('giohang.index') }}" class="btn-view-cart">Xem tất cả</a>
                </div>
            </div>

        </div>

        <!-- Ảnh đại diện -->
        <img src="{{ Auth::user()->anh_dai_dien 
              ? asset('storage/app/public/' . Auth::user()->anh_dai_dien) 
              : asset('public/uploads/default.png') }}"
            alt="Avatar"
            class="avatar1"
            onclick="toggleDropdown()">

        <!-- Dropdown -->
        <div id="dropdownMenu" class="dropdown-menu">
            <ul>
                <!-- Quản lý (chỉ admin) -->
                @if(Auth::user()->role === 'admin')
                <li class="dropdown-item">
                    <a href="{{ route('admin.dashboard') }}">Quản lý</a>
                </li>
                <li class="dropdown-item">
                    <a href="{{ route('admin.doanhnghiep.index') }}">Quản lý doanh nghiệp</a>
                </li>
                <li class="dropdown-item">
                    <a href="{{ route('admin.loaisp.danhsach') }}">
                        Quản lý loại sản phẩm
                    </a>
                </li>
                @endif
                @if(Auth::user()->role === 'doanh_nghiep')

                <li class="dropdown-item">
                    <a href="{{ route('doanhnghiep.quanly') }}">
                        Quản lý doanh nghiệp
                    </a>
                </li>
                @endif
                <li class="dropdown-item">
                    <a href="{{ route('user.show', Auth::id()) }}">
                        Trang cá nhân
                    </a>
                </li>

                <li class="dropdown-item">
                    <a href="{{ route('user.profile') }}">
                        Chỉnh sửa thông tin
                    </a>
                </li>

                <li class="dropdown-item">
                    <a href="{{ route('doanhnghiep.create') }}">
                        Đăng ký doanh nghiệp
                    </a>
                </li>


                <!-- Chế độ tối -->
                <li class="dropdown-item">
                    <button type="button" id="toggle-dark">Chế độ tối</button>
                </li>

                <!-- Đăng xuất -->
                <li class="dropdown-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="color:red;" class="logout-btn">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    @endauth
</nav>
<div id="overlay-cart" class="overlay-cart" onclick="closeCart()"></div>
<script>
    function toggleDropdown() {
        const menu = document.getElementById("dropdownMenu");
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    }

    // Ẩn dropdown nếu click ra ngoài
    document.addEventListener("click", function(event) {
        const menu = document.getElementById("dropdownMenu");
        const avatar = event.target.closest(".avatar1");

        if (!event.target.closest("#dropdownMenu") && !avatar) {
            menu.style.display = "none";
        }
    });
</script>
<script>
    function toggleSearch() {
        const searchInput = document.getElementById('searchInput');
        searchInput.classList.toggle('active');
        if (searchInput.classList.contains('active')) {
            searchInput.focus();
        } else {
            searchInput.blur();
        }
    }

    function toggleThongBao() {
        const box = document.getElementById('notification-box');
        const dot = document.getElementById('notifyDot'); // ✅ sửa id cho đúng
        const isOpen = box.style.display === 'block';

        // Ẩn/hiện khung
        box.style.display = isOpen ? 'none' : 'block';

        // Nếu vừa mở và có chấm đỏ → gửi AJAX đánh dấu đã đọc
        if (!isOpen && dot && dot.style.display !== 'none') {
            fetch("{{ route('thongbao.danhdau') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dot.style.display = "none";
                    }
                });
        }
    }

    window.addEventListener('click', function(e) {
        const icon = document.querySelector('.notification-icon');
        const box = document.getElementById('notification-box');
        if (!icon.contains(e.target) && !box.contains(e.target)) {
            box.style.display = 'none';
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const dot = document.getElementById("cartDotgh");
        const cartViewed = localStorage.getItem("cartViewed"); // trạng thái xem giỏ hàng

        // Ẩn dot nếu số 0 hoặc đã xem giỏ hàng rồi
        if (dot) {
            const quantity = parseInt(dot.textContent);
            if (quantity === 0 || cartViewed === "true") {
                dot.style.display = "none";
            }
        }
    });

    const navLinks = document.querySelectorAll('.nav-center .icon-link');

    navLinks.forEach(link => {
        // Active dựa trên click
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });

        // Active dựa trên URL khi load
        if (link.href === window.location.href) {
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });


    function toggleCart() {
        const cartBox = document.getElementById("cart-box");
        const overlay = document.getElementById("overlay-cart");
        const dot = document.getElementById("cartDotgh");

        const isOpen = cartBox.style.display === "block";

        if (isOpen) {
            // 🔹 Đóng giỏ hàng
            cartBox.style.display = "none";
            overlay.style.display = "none";

            // Nếu có sản phẩm thì hiển thị dot lại
            if (dot && parseInt(dot.textContent) > 0) {
                dot.style.display = "block";
            }
        } else {
            // 🔹 Mở giỏ hàng
            cartBox.style.display = "block";
            overlay.style.display = "block";

            // Khi mở lần đầu → đánh dấu đã xem
            localStorage.setItem("cartViewed", "true");

            // Ẩn dot khi mở
            if (dot) dot.style.display = "none";
        }
    }

    // Khi click ra ngoài overlay → đóng giỏ hàng
    document.getElementById("overlay-cart").addEventListener("click", () => {
        document.getElementById("cart-box").style.display = "none";
        document.getElementById("overlay-cart").style.display = "none";
    });


    function hienDotGioHang(quantity = 1) {
        const cartIcon = document.getElementById("cartIcon");
        if (!cartIcon) return;

        let dot = document.getElementById("cartDotgh");

        if (!dot) {
            dot = document.createElement("span");
            dot.id = "cartDotgh";
            dot.classList.add("dot");
            cartIcon.appendChild(dot);
        }

        // ✅ Cập nhật số lượng hiển thị
        dot.textContent = quantity;

        // ✅ Reset trạng thái đã xem vì có sản phẩm mới
        localStorage.setItem("cartViewed", "false");

        dot.style.display = "block";
    }

    function goToProduct(url) {
        window.location.href = url;
    }
</script>
<script>
    async function updateQuantity(event, form) {
        event.preventDefault(); // chặn reload
        event.stopPropagation();

        const url = form.action;
        const parent = form.closest(".quantity-control");
        const itemRow = form.closest(".cart-item");
        const qtySpan = parent.querySelector(".qty");
        const total = itemRow.querySelector(".item-total");

        try {
            const response = await fetch(url, {
                method: "PATCH",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
            });
            const data = await response.json();

            if (data.deleted) {
                itemRow.remove(); // xóa khỏi giao diện nếu bị xóa
            } else if (data.success) {
                qtySpan.textContent = data.so_luong;
                total.textContent = data.tong;
            }
        } catch (error) {
            console.error("Lỗi cập nhật giỏ hàng:", error);
        }

        return false; // không submit thật
    }
</script>