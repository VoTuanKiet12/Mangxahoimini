@extends('layouts.app')

@section('content')
<div class="container">
    <h2 style="margin-bottom: 20px;">Gợi ý bạn bè</h2>

    @forelse($suggestions as $sg)
    <a href="{{ route('user.show', $sg->id) }}" class="dsgyfriend-link">
        <div class="dsgyfriend-item">
            <div class="dsgyfriend-info">
                <img src="{{ $sg->anh_dai_dien 
                ? asset('storage/app/public/' . $sg->anh_dai_dien) 
                : asset('public/uploads/default.png') }}"
                    alt="{{ $sg->name ?? $sg->username }}"
                    class="avatar-suggest">

                <div class="dsgyfriend-details">
                    <strong>{{ $sg->name ?? $sg->username }}</strong>
                    @if($sg->mutual_count > 0)
                    <br><small class="mutual-count">({{ $sg->mutual_count }} bạn chung)</small>
                    @endif
                </div>
            </div>

            <div class="dsgyfriend-actions">
                <form method="POST" action="{{ route('ketban.send', $sg->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Kết bạn</button>
                </form>
            </div>
        </div>
        @empty
        <p>Không có gợi ý bạn bè nào.</p>
        @endforelse
    </a>
</div>
@endsection

<link rel="stylesheet" href="{{ asset('public/css/dsgybanbe.css') }}">