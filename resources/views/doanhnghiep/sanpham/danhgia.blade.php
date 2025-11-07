@extends('doanhnghiep.quanly')
@section('quanly')
<style>
    .review-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        font-family: "Poppins", sans-serif;
        color: #333;
    }

    .review-container h3 {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 30px;
        position: relative;
    }

    .review-container h3::after {
        content: "";
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, #ff7b00, #ffbb00);
        display: block;
        margin: 8px auto 0;
        border-radius: 2px;
    }

    /* ====== Danh sách đánh giá ====== */
    .review-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .review-item {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 18px 22px;
        transition: 0.3s ease;
    }

    .review-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .review-name {
        font-weight: 600;
        color: #222;
    }

    .review-stars {
        color: #ffb400;
        font-size: 18px;
    }

    .review-content {
        color: #444;
        font-size: 15px;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .review-time {
        font-size: 13px;
        color: #777;
    }

    /* ====== Dark mode ====== */
    .dark-mode .review-item {
        background: #2b2b2b;
        color: #eee;
        box-shadow: none;
    }

    .dark-mode .review-content {
        color: #ccc;
    }

    .dark-mode .review-name {
        color: #fff;
    }

    .dark-mode .review-time {
        color: #aaa;
    }

    .dark-mode .review-container h3 {
        color: #ffffffff;
    }

    .dark-mode .review-item:hover {
        background-color: #444;
    }
</style>

<div class="review-container" data-aos="fade-up">
    <h3>Đánh giá cho sản phẩm: {{ $sanPham->ten_san_pham }}</h3>

    @if($sanPham->danhGia->isEmpty())
    <p style="text-align:center; color:#666;">Chưa có đánh giá nào.</p>
    @else
    <div class="review-list">
        @foreach($sanPham->danhGia as $dg)
        <div class="review-item" data-aos="fade-up">
            <div class="review-header">
                <span class="review-name">{{ $dg->user->name }}</span>
                <span class="review-stars">
                    {!! str_repeat('<i class="bi bi-star-fill"></i>', $dg->so_sao) !!}
                </span>
            </div>
            <p class="review-content">{{ $dg->noi_dung }}</p>
            <small class="review-time">{{ $dg->created_at->diffForHumans() }}</small>
        </div>
        @endforeach
    </div>

    @endif
</div>
@endsection