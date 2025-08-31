{{-- プロフィール設定初回ログイン時のみ /mypage/profile --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/profile-image.js') }}"></script>
@endsection

@section('content')
    <div class="content-container">
        <p class="content-title">プロフィール設定</p>
        <div class="profile-content">
            <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="profile-image-section">
                    <div class="profile-image-container">
                        <div class="profile-image-placeholder" id="profile-image-display">
                            @if ($user->profile && $user->profile->profile_image_path)
                                <img src="{{ asset('storage/' . $user->profile->profile_image_path) }}" alt="プロフィール画像"
                                    class="profile-image-holder">
                            @else
                                <span class="profile-image-text">画像</span>
                            @endif
                        </div>
                        <label for="profile-image" class="profile-image-button button">画像を選択する</label>
                        <input type="file" name="profile-image" id="profile-image" class="profile-image-input"
                            accept="image/*">
                    </div>
                    <p class="form__error">
                        @error('profile-image')
                            {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="form-group">
                    <label class="profile-label" for="name">ユーザー名</label>
                    <input class="profile-input" type="text" name="name" id="name"
                        value="{{ old('name', $user->name) }}" autocomplete="name">
                </div>
                <p class="form__error"></p>
                @error('name')
                    {{ $message }}
                @enderror
                </p>
                <div class="form-group">
                    <label class="profile-label" for="postal_code">郵便番号</label>
                    <input class="profile-input" type="text" name="postal_code" id="postal_code"
                        value="{{ old('postal_code', $profile->postal_code_display ?? '') }}" autocomplete="postal_code">
                </div>
                <p class="form__error">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </p>
                <div class="form-group">
                    <label class="profile-label" for="address">住所</label>
                    <input class="profile-input" type="address" name="address" id="address"
                        value="{{ old('address', $profile->address ?? '') }}" autocomplete="address">
                </div>
                <p class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </p>
                <div class="form-group">
                    <label class="profile-label" for="building_name">建物名</label>
                    <input class="profile-input" type="building_name" name="building_name" id="building_name"
                        value="{{ old('building_name', $profile->building_name ?? '') }}" autocomplete="building_name">
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
