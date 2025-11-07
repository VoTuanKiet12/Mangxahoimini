@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('public/css/dsbanbe.css') }}">
@section('content')
<div class="container">
    <div class="dsbox-friends">
        <h3>Danh sách bạn bè</h3>
        @forelse($friendList as $banbe)
        <a href="{{ route('user.show', $banbe->id) }}" class="dsfriend-item">

            <div class="dsfriend-info">
                <img src="{{ $banbe->anh_dai_dien 
                    ? asset('storage/app/public/' . $banbe->anh_dai_dien) 
                    : asset('public/uploads/default.png') }}"
                    alt="{{ $banbe->name ?? $banbe->username }}"
                    class="dsavatar-friend">

                <span class="dsfriend-name">{{ $banbe->name ?? $banbe->username }}</span>
            </div>


            <form method="POST" action="{{ route('ketban.cancel', $banbe->id) }}" class="friend-actions"
                onclick="event.stopPropagation();">
                @csrf
                <button type="submit" class="btn-remove-friend">Hủy kết bạn</button>
            </form>
        </a>
        @empty
        <p>Bạn chưa có bạn bè nào.</p>
        @endforelse
    </div>
</div>
@endsection