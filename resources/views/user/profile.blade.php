@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/ttcn.css') }}">

@section('content')
<div class="container mt-4" data-aos="fade-down">


    <div class="form-all">
        <h2>Thông tin cá nhân</h2>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('user.profile.update') }}" method="POST">
            @csrf
            <table class="table-form">
                <tr>
                    <td><label for="name">Họ và tên</label></td>
                    <td>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required>
                    </td>
                </tr>

                <tr>
                    <td><label for="dia_chi">Địa chỉ</label></td>
                    <td>
                        <input type="text" class="form-control" id="dia_chi" name="dia_chi"
                            value="{{ old('dia_chi', $user->dia_chi) }}">
                    </td>
                </tr>

                <tr>
                    <td><label for="so_dien_thoai">Số điện thoại</label></td>
                    <td>
                        <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai"
                            value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}">
                    </td>
                </tr>

                <tr>
                    <td><label for="ngay_sinh">Ngày sinh</label></td>
                    <td>
                        <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh"
                            value="{{ old('ngay_sinh', $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('Y-m-d') : '') }}">
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
@endsection