@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-edit">

    <h2 class="address-edit__title">住所の変更</h2>

    @if (session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <form action="{{ route('address.update') }}" method="POST" class="address-edit__form">
        @csrf

        <div class="form-group">
            <label class="form-label">郵便番号</label>
            <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code', $address->postal_code ?? '') }}">
            @error('postal_code')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">住所</label>
            <input type="text" name="address" class="form-input" value="{{ old('address', $address->address ?? '') }}">
            @error('address')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">建築名</label>
            <input type="text" name="building" class="form-input" value="{{ old('building', $address->building ?? '') }}">
            @error('building')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button class="address-edit__button">更新する</button>
    </form>

</div>
@endsection