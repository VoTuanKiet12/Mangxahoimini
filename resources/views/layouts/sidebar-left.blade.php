<button class="sidebar-toggle-left" id="sidebarToggleLeft">
    &#9776;
</button>
<aside class="sidebar-left" id="sidebarLeft">
    <ul>

        <p class="titel-left"><i class="bi bi-cart2"></i> Mini shop</p>
        <a href="{{ route('donhang.daMua') }}" class="btn btn-primary">
            <li><i class="bi bi-people-fill"></i> Đơn hàng của bạn</li>
        </a>
        <a href="{{ route('sanpham.index') }}">
            <li><i class="bi bi-shop"></i> Cửa hàng</li>
        </a>
    </ul>

</aside>