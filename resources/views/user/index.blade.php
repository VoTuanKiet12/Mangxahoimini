@extends('layouts.app')

@section('title', 'Người dùng')

@section('content')
<h1>Danh sách người dùng</h1>
@foreach($users as $u)
<div class="card" style="margin-bottom: 10px; padding: 10px;">
    <b>{{ $u->ho_ten }}</b> ({{ $u->ten_dang_nhap }}) - {{ $u->vai_tro }}

    {{-- Hiển thị ảnh đại diện --}}
    @if($u->anh_dai_dien)
    <div>
        <img src="{{ asset('storage/' . $u->anh_dai_dien) }}"
            alt="Ảnh đại diện"
            width="80" height="80"
            style="border-radius: 50%; object-fit: cover;">
    </div>
    @else
    <p><i>Chưa có ảnh đại diện</i></p>
    @endif
</div>
@endforeach
@endsection