@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/sanphamct.css') }}">
@section('title', $sanPham->ten_san_pham)

@section('full')
<div class="product-detail-container">
    <div class="product-detail">

        {{-- === Ảnh sản phẩm === --}}
        @php $images = json_decode($sanPham->hinh_anh, true); @endphp
        <div class="image-slider" id="product-{{ $sanPham->id }}">
            <button class="prev-btn"><i class="bi bi-caret-left-fill"></i></button>
            <img id="mainImage" src="{{ asset('public/storage/' . ($images[0] ?? 'default.png')) }}" alt="Ảnh sản phẩm">
            <button class="next-btn"><i class="bi bi-caret-right-fill"></i></button>
            <div class="cham-bar" id="dotBar">
                @foreach($images as $index => $img)
                <span class="cham {{ $index === 0 ? 'active' : '' }}" onclick="setImage({{ $index }})"></span>
                @endforeach
            </div>
        </div>



        {{-- === Thông tin sản phẩm === --}}
        <div class="product-info">
            <div class="product-info-if">
                @php
                $avgRating = round($sanPham->danhGia->avg('so_sao'), 1);
                @endphp

                @if($sanPham->danhGia->count() > 0)
                <p class="rating">
                    <span>({{ $sanPham->danhGia->count() }})</span>
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= $avgRating ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                        @endfor

                </p>
                @else
                <p class="rating-no">Chưa có đánh giá</p>
                @endif
                <h2>{{ $sanPham->ten_san_pham }}</h2>

                @if ($sanPham->khuyenMaiHienTai)
                <p class="price">
                    <span class="old-price" style="text-decoration: line-through; color: #888;">
                        {{ number_format($sanPham->gia, 0, ',', '.') }}₫
                    </span>
                    <span class="new-price" style="color: #e53935; font-weight: bold; margin-left: 8px;">
                        {{ number_format($sanPham->gia_sau_khuyen_mai, 0, ',', '.') }}₫
                    </span>
                    <small class="discount" style="color: #2e7d32; font-size: 0.9rem; margin-left: 4px;">
                        -{{ $sanPham->khuyenMaiHienTai->muc_giam }}%
                    </small>
                </p>
                @else
                <p class="price">
                    {{ number_format($sanPham->gia, 0, ',', '.') }}₫
                </p>
                @endif


                <p><strong>Số lượng:</strong> {{ $sanPham->so_luong }}</p>
                <p><strong>Doanh nghiệp:</strong> {{ $sanPham->doanhNghiep->ten_cua_hang ?? 'Ẩn danh' }}</p>
                {{-- === Chọn số lượng === --}}
                <div class="quantity-control-sp"
                    id="quantityControl-sp"
                    data-max="{{ $sanPham->so_luong }}">
                    <button type="button" class="btn-qty-sp decrease">−</button>
                    <span class="qty-sp" id="qtyValue-sp">1</span>
                    <button type="button" class="btn-qty-sp increase">+</button>
                </div>


                <div class="action-buttons">
                    @csrf
                    @if ($sanPham->so_luong > 0)
                    <button type="button" class="btn btn-cart" onclick="themVaoGioHang({{ $sanPham->id }})">
                        <i class="bi bi-cart"></i>
                    </button>
                    <button type="button" class="btn btn-buy" onclick="muaNgay({{ $sanPham->id }})">
                        Mua ngay
                    </button>
                    @else
                    <button type="button" class="btn btn-out" disabled style="background-color: gray; cursor: not-allowed;">
                        Hết hàng
                    </button>
                    @endif
                </div>

                <div class="mo-ta-container">
                    <div class="mo-ta-title" id="toggleMoTa">
                        <strong> Mô tả sản phẩm</strong>
                        <span class="toggle-icon" id="toggleIcon">▼</span>
                    </div>

                    <div class="mo-ta-noidung" id="moTaContent">
                        {!! nl2br(e($sanPham->mo_ta)) !!}
                    </div>
                </div>
                <div class="mo-ta-container">
                    <div class="mo-ta-title" id="toggleMoTa2">
                        <strong>Thông tin bảo hành</strong>
                        <span class="toggle-icon" id="toggleIcon2">▼</span>
                    </div>

                    <div class="mo-ta-noidung" id="moTaContent2">
                    </div>
                </div>




                @if(isset($goiY) && $goiY->count() > 0)
                <div class="related-products">
                    <h3>Sản phẩm tương tự</h3>
                    <div class="related-grid">
                        @foreach($goiY as $sp)
                        @php
                        $imgs = json_decode($sp->hinh_anh, true);
                        $img = $imgs[0] ?? 'default.png';
                        @endphp
                        <a href="{{ route('sanpham.chitiet', $sp->id) }}" class="related-card">
                            <img src="{{ asset('public/storage/' . $img) }}" alt="{{ $sp->ten_san_pham }}">
                            <div class="related-info">
                                <p class="ten">{{ $sp->ten_san_pham }}</p>
                                {{-- ✅ Hiển thị giá có khuyến mãi nếu có --}}
                                @if ($sp->khuyenMaiHienTai)
                                <p class="gia">
                                    <span class="old-gia" style="text-decoration: line-through; color: #888;">
                                        {{ number_format($sp->gia, 0, ',', '.') }}₫
                                    </span>
                                    <span class="new-gia" style="color: #e53935; font-weight: bold; margin-left: 8px;">
                                        {{ number_format($sp->gia_sau_khuyen_mai, 0, ',', '.') }}₫
                                    </span>
                                    <span class="discount" style="color: #2e7d32; font-size: 0.9em; margin-left: 4px;">
                                        -{{ $sp->khuyenMaiHienTai->muc_giam }}%
                                    </span>
                                </p>
                                @else
                                <p class="gia">
                                    {{ number_format($sp->gia, 0, ',', '.') }}₫
                                </p>
                                @endif

                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="review-section">
                <h3>Đánh giá sản phẩm</h3>
                @php
                $tongDanhGia = $sanPham->danhGia->count();
                $trungBinhSao = $tongDanhGia > 0 ? round($sanPham->danhGia->avg('so_sao'), 1) : 0;
                $demSao = [
                5 => $sanPham->danhGia->where('so_sao', 5)->count(),
                4 => $sanPham->danhGia->where('so_sao', 4)->count(),
                3 => $sanPham->danhGia->where('so_sao', 3)->count(),
                2 => $sanPham->danhGia->where('so_sao', 2)->count(),
                1 => $sanPham->danhGia->where('so_sao', 1)->count(),
                ];
                @endphp

                <div class="rating-summary">
                    <div class="rating-left">
                        <h1>{{ number_format($trungBinhSao, 1) }}</h1>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $trungBinhSao ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                @endfor
                        </div>
                        <p>{{ $tongDanhGia }} đánh giá</p>
                    </div>
                    <div class="rating-bars">
                        @foreach([5,4,3,2,1] as $sao)
                        @php
                        $phanTram = $tongDanhGia > 0 ? ($demSao[$sao] / $tongDanhGia) * 100 : 0;
                        @endphp
                        <div class="bar-row">
                            <span>{{ $sao }} <i class="bi bi-star-fill text-warning"></i></span>
                            <div class="bar">
                                <div class="fill" style="width: {{ $phanTram }}%;"></div>
                            </div>
                            <span>{{ $demSao[$sao] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="review-list">
                    @forelse($sanPham->danhGia as $dg)
                    <div class="review-item">
                        <div class="review-header">
                            <strong>{{ $dg->user->name ?? 'Người dùng ẩn danh' }}</strong>
                            <div class="stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $dg->so_sao ? 'filled' : '' }}"><i class="bi bi-star-fill"></i></span>
                                    @endfor
                            </div>
                            <small>{{ $dg->created_at->format('d/m/Y') }}</small>
                            @if(Auth::check() && (Auth::id() === $dg->user_id || Auth::user()->role === 'admin'))
                            <form action="{{ route('danhgia.destroy', $dg->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-review" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        <p class="review-content">{{ $dg->noi_dung }}</p>
                        @if($dg->hinh_anh)
                        <div class="review-image">
                            <img src="{{ asset('public/storage/' . $dg->hinh_anh) }}" alt="Ảnh đánh giá" class="review-photo">
                        </div>
                        @endif

                    </div>

                    @empty
                    <p class="no-review">Chưa có đánh giá nào cho sản phẩm này.</p>
                    @endforelse
                </div>
                {{-- ✅ Form gửi đánh giá --}}
                @auth
                <form action="{{ route('danhgia.store') }}" method="POST" class="review-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="san_pham_id" value="{{ $sanPham->id }}">

                    <div class="rating">
                        @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="so_sao" value="{{ $i }}"
                            {{ $i == 1 ? 'checked' : '' }} required>
                        <label for="star{{ $i }}" title="{{ $i }} sao">
                            <i class="bi bi-star-fill"></i>
                        </label>
                        @endfor
                    </div>


                    <textarea name="noi_dung" rows="3" placeholder="Viết đánh giá của bạn..." required></textarea>

                    {{-- 🖼️ Thêm ảnh đánh giá --}}
                    <div class="review-image-upload">
                        <label for="hinh_anh" class="btn-upload-image">
                            <i class="bi bi-image-fill"> Up ảnh</i>
                        </label>
                        <input type="file" name="hinh_anh" id="hinh_anh" accept="image/*" hidden>
                        <span id="file-name" class="file-name">Chưa chọn ảnh</span>
                    </div>


                    <button type="submit" class="btn btn-send-review">Gửi đánh giá</button>

                </form>

                @else
                <p class="login-notice">Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đánh giá sản phẩm.</p>
                @endauth
            </div>
        </div>
    </div>



</div>


<script>
    document.querySelectorAll('.mo-ta-title').forEach(title => {
        const icon = title.querySelector('.toggle-icon');
        const content = title.nextElementSibling;
        let isOpen = false;

        title.addEventListener('click', () => {
            isOpen = !isOpen;
            if (isOpen) {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.textContent = "▲";
            } else {
                content.style.maxHeight = "0px";
                icon.textContent = "▼";
            }
        });
    });
</script>
<script>
    document.getElementById('hinh_anh').addEventListener('change', function() {
        const fileName = this.files.length ? this.files[0].name : 'Chưa chọn ảnh';
        document.getElementById('file-name').textContent = fileName;
    });
</script>
<script>
    function themVaoGioHang(sanPhamId) {
        // Gọi API thêm vào giỏ hàng
        fetch("{{ route('giohang.them') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    san_pham_id: sanPhamId,
                    so_luong: 1
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    hienHieuUngBay(sanPhamId); // hiệu ứng bay
                    capNhatMiniGioHang();
                    hienDotGioHang();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(err => console.error(err));
    }

    // Hiệu ứng bay ảnh sản phẩm vào giỏ hàng
    function hienHieuUngBay(sanPhamId) {
        const productImg = document.querySelector(`#product-${sanPhamId} img`);
        const cartIcon = document.getElementById("cartIcon");
        if (!productImg || !cartIcon) return;

        const flyingImg = productImg.cloneNode(true);
        flyingImg.style.position = "fixed";
        flyingImg.style.zIndex = 9999;
        flyingImg.style.width = productImg.offsetWidth + "px";
        flyingImg.style.height = productImg.offsetHeight + "px";
        flyingImg.style.left = productImg.getBoundingClientRect().left + "px";
        flyingImg.style.top = productImg.getBoundingClientRect().top + "px";
        flyingImg.style.transition = "all 0.8s ease-in-out";
        flyingImg.style.borderRadius = "10px";
        flyingImg.style.opacity = "1";

        document.body.appendChild(flyingImg);

        const cartRect = cartIcon.getBoundingClientRect();

        setTimeout(() => {
            flyingImg.style.left = cartRect.left + "px";
            flyingImg.style.top = cartRect.top + "px";
            flyingImg.style.width = "25px";
            flyingImg.style.height = "25px";
            flyingImg.style.opacity = "0.3";
        }, 10);

        flyingImg.addEventListener("transitionend", () => {
            flyingImg.remove();


            cartIcon.classList.add("shake", "glow");
            setTimeout(() => {
                cartIcon.classList.remove("shake", "glow");
            }, 800);
        });
    }


    function capNhatMiniGioHang() {
        fetch("{{ route('giohang.index') }}", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.text())
            .then(html => {
                // Tạo DOM tạm để lấy phần mini-cart
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newCartBox = doc.querySelector("#cart-box");

                // Thay thế mini-cart hiện tại
                const currentCartBox = document.querySelector("#cart-box");
                if (newCartBox && currentCartBox) {
                    currentCartBox.innerHTML = newCartBox.innerHTML;
                }
            })
            .catch(err => console.error("Lỗi cập nhật giỏ hàng:", err));
    }
</script>


<script>
    function muaNgay(id) {
        const quantity = document.getElementById("qtyValue-sp")?.textContent || 1;
        window.location.href = "{{ url('/donhang/muangay') }}/" + id + "?so_luong=" + quantity;
    }
</script>
<script>
    const images = @json($images ?? []);
    let currentIndex = 0;
    const mainImage = document.getElementById('mainImage');
    const dots = document.querySelectorAll('#dotBar .cham');

    function updateImage() {
        if (images.length === 0) return;
        mainImage.src = "{{ asset('public/storage') }}/" + images[currentIndex];
        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
    }

    document.querySelector('.prev-btn').addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateImage();
    });

    document.querySelector('.next-btn').addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % images.length;
        updateImage();
    });

    function setImage(index) {
        currentIndex = index;
        updateImage();
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const quantityControl = document.getElementById("quantityControl-sp");
        if (!quantityControl) return;

        let quantity = 1;
        const maxQuantity = parseInt(quantityControl.dataset.max) || 1;

        const qtyValue = document.getElementById("qtyValue-sp");
        const decreaseBtn = quantityControl.querySelector(".btn-qty-sp.decrease");
        const increaseBtn = quantityControl.querySelector(".btn-qty-sp.increase");

        decreaseBtn.addEventListener("click", () => {
            if (quantity > 1) {
                quantity--;
                qtyValue.textContent = quantity;
            }
        });

        increaseBtn.addEventListener("click", () => {
            if (quantity < maxQuantity) {
                quantity++;
                qtyValue.textContent = quantity;
            } else {
                alert(`Số lượng tối đa là ${maxQuantity}!`);
            }
        });
    });
</script>

@endsection