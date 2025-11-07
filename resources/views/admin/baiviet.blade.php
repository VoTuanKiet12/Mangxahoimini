@extends('admin.dashboard')

@section('title', 'Quản lý bài viết')
<link rel="stylesheet" href="{{ asset('public/css/adminbaiviet.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@section('quanly')
<div class="dashboard-container">
    <h2 class="dashboard-title">Quản lý bài viết</h2>

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

    <h3 class="dashboard-subtitle">Danh sách bài viết</h3>

    <table class="dashboard-table" data-aos="fade-up">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người đăng</th>
                <th>Nội dung</th>
                <th>Phương tiện</th>
                <th>Ngày đăng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($baiViet as $bv)
            <tr>
                <td>{{ $bv->id }}</td>
                <td>{{ $bv->user->username ?? 'Ẩn danh' }}</td>
                <td>{{ $bv->noi_dung ?? '(Không có nội dung)' }}</td>
                <td>
                    {{-- Ảnh --}}
                    @php
                    $images = is_array($bv->hinh_anh) ? $bv->hinh_anh : json_decode($bv->hinh_anh, true);
                    @endphp

                    @if(!empty($images))
                    @foreach($images as $img)
                    <img src="{{ asset('storage/app/public/' . $img) }}" alt="Ảnh bài viết"
                        width="60" style="margin: 3px; border-radius: 6px;">
                    @endforeach
                    @elseif($bv->video)
                    {{-- Video --}}
                    <video width="120" controls>
                        <source src="{{ asset('storage/app/public/' . $bv->video) }}" type="video/mp4">
                        Trình duyệt không hỗ trợ video.
                    </video>
                    @else
                    <span>Không có</span>
                    @endif
                </td>
                <td>{{ $bv->ngay_dang ? \Carbon\Carbon::parse($bv->ngay_dang)->format('d/m/Y H:i') : '—' }}</td>
                <td>
                    <form action="{{ route('admin.baiviet.destroy', $bv->id) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash-fill"></i> Xóa
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $baiViet->links() }}
    </div>
</div>
@endsection