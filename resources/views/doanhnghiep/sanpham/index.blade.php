@extends('doanhnghiep.quanly')
@section('title', 'Quản lý sản phẩm')
<link rel="stylesheet" href="{{ asset('public/css/sanphamql.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="{{ asset('public/vendor/font-awesome/css/all.min.css') }}" />

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@section('quanly')



<div class="containerqlsp" data-aos="fade-left">
    <h3>Quản lý sản phẩm </h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="top-bar">
        <div class="actions-left">
            <a href="{{ route('doanhnghiep.dangsanpham') }}" class="btn btn-primary">+ Thêm sản phẩm</a>
            <a href="#nhap" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fa-light fa-upload"></i> Nhập từ Excel</a>
            <a href="{{ route('doanhnghiep.sanpham.xuat') }}" class="btn btn-success"><i class="fa-light fa-download"></i> Xuất ra Excel</a>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="pagination-formtop">
            <label class="trang" for="pageInput">Trang:</label>
            <input type="number" id="pageInput" name="page"
                min="1"
                max="{{ $sanPhams->lastPage() }}"
                value="{{ $sanPhams->currentPage() }}"
                class="page-input">
            <button type="submit" class="btn-page-go">Go</button>
        </form>
    </div>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Loại</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Trạng thái</th>
                <th>Ngày đăng</th>
                <th>Sửa</th>
                <th>Xóa</th>
                <th>Đánh giá</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sanPhams as $sp)
            <tr>
                <td>{{ $sp->id }}</td>
                <td>
                    @php
                    $images = json_decode($sp->hinh_anh, true);
                    @endphp
                    @if($images && count($images) > 0)
                    <img src="{{ asset('public/storage/' . $images[0]) }}" class="product-img" alt="Ảnh sản phẩm">
                    @else
                    <img src="{{ asset('public/storage/img/no-image.png') }}" class="product-img" alt="Không có ảnh">
                    @endif
                </td>
                <td>{{ $sp->ten_san_pham }}</td>
                <td>{{ $sp->loaiSanPham->ten_loai ?? 'Không xác định' }}</td>
                <td>{{ number_format($sp->gia) }}₫</td>
                <td>{{ $sp->so_luong }}</td>
                <td>
                    @if($sp->so_luong == 0)
                    <span class="badge bg-danger">Hết hàng</span>
                    @elseif($sp->trang_thai == 'an')
                    <span class="badge bg-secondary">Ẩn</span>
                    @else
                    <span class="badge bg-success">Còn hàng</span>
                    @endif
                </td>
                <td>{{ $sp->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('doanhnghiep.sanpham.edit', $sp->id) }}" class="btn btn-warning">Sửa</a>

                </td>
                <td>
                    <form action="{{ route('doanhnghiep.sanpham.delete', $sp->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')" class="btn btn-danger">Xóa</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('doanhnghiep.sanpham.danhgia', $sp->id) }}" class="danhgia">
                        Đánh giá
                    </a>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="9">Chưa có sản phẩm nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    <div class="pagination-container">
        <div class="pagination-links">
            {{ $sanPhams->links() }}
        </div>
        <form action="{{ url()->current() }}" method="GET" class="pagination-form">
            <label class="trang" for="pageInput">Trang:</label>
            <input type="number" id="pageInput" name="page"
                min="1"
                max="{{ $sanPhams->lastPage() }}"
                value="{{ $sanPhams->currentPage() }}"
                class="page-input">
            <button type="submit" class="btn-page-go">Go</button>
        </form>


    </div>




</div>
<form action="{{ route('sanpham.nhap') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Nhập từ Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label for="file_excel" class="form-label">Chọn tập tin Excel</label>
                        <input type="file" class="form-control" id="file_excel" name="file_excel" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa-light fa-upload"></i> Nhập dữ liệu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-light fa-times"></i> Hủy bỏ</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection