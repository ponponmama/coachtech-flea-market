{{-- プロフィール設定初回ログイン時のみ /mypage/profile --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <p class="auth-title">プロフィール設定</p>
        <div class="auth-content-profile">
            <span class="profile-image" for="profile-image">画像を選択する</span>
            <input type="file" name="profile-image" id="profile-image" class="profile-image-input">
        </div>
        <div class="auth-content">
            <form action="{{ route('mypage.profile.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="profile-label" for="name">ユーザー名</label>
                    <input class="profile-input" type="text" name="name" id="name" value="{{ old('name') }}"
                        autocomplete="name">
                </div>
                <p class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </p>
                <div class="form-group">
                    <label class="profile-label" for="postal_code">郵便番号</label>
                    <input class="profile-input" type="postal_code" name="postal_code" id="postal_code"
                        value="{{ old('postal_code') }}" autocomplete="postal_code">
                </div>
                <p class="form__error">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </p>
                <div class="form-group">
                    <label class="profile-label" for="address">住所</label>
                    <input class="profile-input" type="address" name="address" id="address" value="{{ old('address') }}"
                        autocomplete="address">
                </div>
                <p class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </p>
                <div class="form-group">
                    <label class="profile-label" for="building_name">建物名</label>
                    <input class="profile-input" type="building_name" name="building_name" id="building_name"
                        value="{{ old('building_name') }}" autocomplete="building_name">
                </div>
                <p class="form__error">
                    @error('building_name')
                        {{ $message }}
                    @enderror
                </p>
                <button type="submit" class="profile-button button">更新する</button>
            </form>
        </div>
    </div>
@endsection
