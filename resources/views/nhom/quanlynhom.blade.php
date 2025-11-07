@extends('layouts.app')
@section('title', 'Quản lý nhóm - ' . $nhom->ten_nhom)
<link rel="stylesheet" href="{{ asset('public/css/nhomql.css') }}">

@section('content')
<div class="group-container">
    <h2>Quản lý nhóm: {{ $nhom->ten_nhom }}</h2>
    <p><strong>Chủ nhóm:</strong> {{ $nhom->chuNhom->name }}</p>
    <hr>

    @if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert error">{{ session('error') }}</div>
    @endif

    <table class="member-table">
        <thead>
            <tr>
                <th>Ảnh đại diện</th>
                <th>Tên thành viên</th>
                <th>Vai trò</th>
                <th>Ngày tham gia</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nhom->users as $user)
            <tr>
                <td>
                    <img src="{{ $user->anh_dai_dien 
                        ? asset('storage/app/public/'.$user->anh_dai_dien) 
                        : asset('public/uploads/default.png') }}"
                        alt="avatar" class="avatar">
                </td>
                <td>{{ $user->name }}</td>
                <td>
                    @if($user->pivot->vai_tro !== 'chu_nhom' && in_array($vaiTro, ['chu_nhom', 'quan_tri_vien']))
                    <form action="{{ route('nhom.updateRole', [$nhom->id, $user->id]) }}" method="POST" class="inline-form">
                        @csrf
                        <select name="vai_tro" class="role-select">
                            <option value="thanh_vien" {{ $user->pivot->vai_tro === 'thanh_vien' ? 'selected' : '' }}>Thành viên</option>
                            <option value="quan_tri_vien" {{ $user->pivot->vai_tro === 'quan_tri_vien' ? 'selected' : '' }}>Quản trị viên</option>
                        </select>
                        <button class="btn btn-blue">Lưu</button>
                    </form>
                    @else
                    <span class="badge {{ $user->pivot->vai_tro }}">
                        {{ ucfirst(str_replace('_', ' ', $user->pivot->vai_tro)) }}
                    </span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($user->pivot->ngay_tham_gia)->format('d/m/Y H:i') }}</td>
                <td>
                    @if($user->pivot->vai_tro !== 'chu_nhom' && in_array($vaiTro, ['chu_nhom', 'quan_tri_vien']))
                    <form action="{{ route('nhom.kick', [$nhom->id, $user->id]) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn kick thành viên này?')" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-red">Kick</button>
                    </form>
                    @else
                    <em>Không thể</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection