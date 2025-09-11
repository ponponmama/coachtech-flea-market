{{-- 送付先住所変更画面 - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <h1 class="content-title">住所の変更</h1>

        <div class="address-form-section">
            <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="address-form">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="postal_code">郵便番号</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-input"
                        value="{{ old('postal_code', $currentAddress['postal_code'] ?? '') }}" placeholder="例: 123-4567"
                        autocomplete="postal-code">
                </div>
                <p class="form__error">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </p>

                <div class="form-group">
                    <label class="form-label" for="address">住所</label>
                    <input type="text" name="address" id="address" class="form-input"
                        value="{{ old('address', $currentAddress['address'] ?? '') }}" placeholder="例: 東京都渋谷区..."
                        autocomplete="address-line1">
                </div>
                <p class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </p>

                <div class="form-group">
                    <label class="form-label" for="building">建物名</label>
                    <input type="text" name="building" id="building" class="form-input"
                        value="{{ old('building', $currentAddress['building'] ?? '') }}" placeholder="例: マンション名 101号室"
                        autocomplete="address-line2">
                </div>
                <p class="form__error">
                    @error('building')
                        {{ $message }}
                    @enderror
                </p>

                <div class="form-group">
                    <button type="submit" class="update-button">更新する</button>
                </div>
            </form>
        </div>
    </div>
@endsection
