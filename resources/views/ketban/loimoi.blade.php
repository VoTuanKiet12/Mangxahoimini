@extends('layouts.app')

@section('content')
<div class="containerdslm">
    <h2>Tất cả lời mời kết bạn</h2>

    @forelse($requests as $req)
    <div class="invite-itemdslm">
        <img src="{{ $req->user->anh_dai_dien 
                ? asset('storage/app/public/' . $req->user->anh_dai_dien) 
                : asset('public/uploads/default.png') }}"
            alt="avatar" class="avatar-invitedslm">

        <span class="invite-namedslm">
            {{ $req->user->name ?? $req->user->username }}
        </span>

        <div class="invite-actionsdslm">
            <form method="POST" action="{{ route('ketban.accept', $req->id) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
            </form>
            <form method="POST" action="{{ route('ketban.decline', $req->id) }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Từ chối</button>
            </form>
        </div>
    </div>
    @empty
    <p>Không có lời mời kết bạn nào.</p>
    @endforelse
</div>
@endsection

<link rel="stylesheet" href="{{ asset('public/css/dsloimoi.css') }}">