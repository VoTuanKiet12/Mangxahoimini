@extends('layouts.app')

@section('title', 'Tìm kiếm người dùng')

@section('content')
<div class="search-container-page">
    <h3 class="page-title"> Tìm kiếm người dùng</h3>

    {{-- Kết quả --}}
    @if(!empty($keyword))
    @if($users->isEmpty())
    <p class="no-result">Không tìm thấy người dùng nào phù hợp với "{{ $keyword }}".</p>
    @else
    <div class="result-list">
        @foreach($users as $user)
        <div class="user-item">
            <a href="{{ route('user.show', $user->id) }}" class="user-link" style="text-decoration: none;">
                <img src="{{ $user->anh_dai_dien 
                            ? asset('storage/app/public/' . $user->anh_dai_dien) 
                            : asset('public/uploads/default.png') }}"
                    alt="{{ $user->name ?? $user->username }}" class="avatar">

                <div class="user-info">
                    <strong>{{ $user->name }}</strong>
                </div>
            </a>

            {{-- Nút tùy theo trạng thái bạn bè --}}
            @if(is_null($user->friend_status))
            <form method="POST" action="{{ route('ketban.send', $user->id) }}">
                @csrf
                <button type="submit" class="btn-add-friend">Kết bạn</button>
            </form>
            @elseif($user->friend_status === 'cho')
            <button class="btn-pending" disabled>Đã gửi lời mời</button>
            @elseif($user->friend_status === 'chap_nhan')
            <button class="btn-friend" disabled>Bạn bè</button>
            @endif
        </div>
        @endforeach
    </div>
    @endif
    @else
    <p class="no-keyword">Hãy nhập tên người dùng để bắt đầu tìm kiếm...</p>
    @endif
</div>


<link rel="stylesheet" href="{{ asset('public/css/timkiem.css') }}">
@endsection