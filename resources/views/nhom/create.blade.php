@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/taonhom.css') }}">
@section('title', 'Tạo nhóm mới')

@section('content')
<div class="create-container">
    <div class="create-title">TẠO NHÓM MỚI</div>

    @if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('nhom.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label class="form-label">Tên nhóm *</label>
            <input type="text" name="ten_nhom" class="form-input" value="{{ old('ten_nhom') }}" required>
            @error('ten_nhom')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Mô tả nhóm</label>
            <textarea name="mo_ta" class="form-textarea">{{ old('mo_ta') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Chế độ nhóm *</label>
            <select name="che_do" class="form-select" required>
                <option value="cong_khai">Công khai</option>
                <option value="kin">Kín</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Ảnh bìa nhóm</label>
            <input type="file" name="anh_bia" class="form-file">
        </div>

        <div class="text-center">
            <button type="submit" class="btn-submit">Tạo nhóm</button>
        </div>
    </form>
</div>
@endsection