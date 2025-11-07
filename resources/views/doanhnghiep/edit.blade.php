@extends('doanhnghiep.quanly')

@section('quanly')
<style>
    .dark-mode .edit-container {
        background: #292929ff;
    }

    .dark-mode .edit-container h2,
    .dark-mode form label {
        color: #ffffffff;
    }

    .edit-container {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    .edit-container h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }

    form label {
        font-weight: bold;
        color: #2c3e50;
        display: block;
        margin-bottom: 5px;
    }

    form input,
    form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 15px;
    }

    form input[type="file"] {
        padding: 5px;
    }

    .logo-preview {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #eee;
    }

    button {
        background-color: #2e3d4dff;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
    }

    button:hover {
        background-color: #5a7694ff;
    }
</style>

<div class="edit-container">
    <h2>Chỉnh sửa thông tin cửa hàng</h2>

    <form action="{{ route('doanhnghiep.update', $doanhNghiep->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="logo-preview">
            <label>Logo hiện tại:</label><br>
            @if($doanhNghiep->logo)
            <img src="{{ asset('public/storage/' . $doanhNghiep->logo) }}" alt="Logo cửa hàng">
            @else
            <img src="https://via.placeholder.com/100x100.png?text=No+Logo" alt="No logo">
            @endif
        </div>

        <label for="logo">Cập nhật logo mới:</label>
        <input type="file" name="logo" id="logo" accept="image/*">

        <label for="ten_cua_hang">Tên cửa hàng:</label>
        <input type="text" name="ten_cua_hang" id="ten_cua_hang"
            value="{{ old('ten_cua_hang', $doanhNghiep->ten_cua_hang) }}"
            readonly
            style="background-color:#f8f9fa; cursor:not-allowed;">


        <label for="mo_ta">Mô tả:</label>
        <textarea name="mo_ta" id="mo_ta" rows="4">{{ old('mo_ta', $doanhNghiep->mo_ta) }}</textarea>

        <label for="dia_chi">Địa chỉ:</label>
        <input type="text" name="dia_chi" id="dia_chi" value="{{ old('dia_chi', $doanhNghiep->dia_chi) }}">

        <label for="so_dien_thoai">Số điện thoại:</label>
        <input type="text" name="so_dien_thoai" id="so_dien_thoai" value="{{ old('so_dien_thoai', $doanhNghiep->so_dien_thoai) }}">

        <button type="submit">Lưu thay đổi</button>
    </form>
</div>
@endsection