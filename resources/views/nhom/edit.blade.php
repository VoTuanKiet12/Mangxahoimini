@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/nhomedit.css') }}">
@section('title', 'Chỉnh sửa nhóm')

@section('content')
<div class="container-nhom">
    <h3>CHỈNH SỬA THÔNG TIN NHÓM</h3>

    <form action="{{ route('nhom.update', $nhom->id) }}" method="POST" enctype="multipart/form-data" class="form-create">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="ten_nhom">Tên nhóm</label>
            <input type="text" name="ten_nhom" id="ten_nhom" class="form-control" value="{{ old('ten_nhom', $nhom->ten_nhom) }}" required>
        </div>

        <div class="form-group">
            <label for="mo_ta">Mô tả</label>
            <textarea name="mo_ta" id="mo_ta" class="form-control" rows="3">{{ old('mo_ta', $nhom->mo_ta) }}</textarea>
        </div>

        <div class="form-group">
            <label for="che_do">Chế độ</label>
            <select name="che_do" id="che_do" class="form-control">
                <option value="cong_khai" {{ $nhom->che_do == 'cong_khai' ? 'selected' : '' }}>Công khai</option>
                <option value="kin" {{ $nhom->che_do == 'kin' ? 'selected' : '' }}>Kín</option>
            </select>
        </div>

        <div class="form-group">
            <label for="anh_bia">Ảnh bìa nhóm (tùy chọn)</label>
            @if ($nhom->anh_bia)
            <div style="margin-bottom:10px;">
                <img src="{{ asset('storage/app/public/' . $nhom->anh_bia) }}" alt="Ảnh bìa hiện tại" width="100%">
            </div>
            @endif
            <input type="file" name="anh_bia" id="anh_bia" class="form-control">
        </div>

        <button type="submit" class="btn-view">Lưu thay đổi</button>
        <a href="{{ route('nhom.index') }}" class="btn-reject">Hủy</a>
    </form>
</div>
@endsection