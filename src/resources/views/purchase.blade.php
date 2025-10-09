{{-- 商品購入画面 - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('js')
    {{-- Stripe決済処理用のJavaScript（外部ファイル） --}}
    <script src="{{ asset('js/purchase.js') }}"></script>
@endsection

@section('content')
    <div class="content-container" data-item-id="{{ $item->id }}">
        <!-- 左側：商品情報・支払い・配送先 -->
        <div class="purchase-section">
            <div class="product-info-section">
                <div class="info-group">
                    <div class="product-image-container">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                            class="product-image">
                    </div>
                    <div class="product-details">
                        <p class="product-name">
                            {{ $item->name }}
                        </p>
                        <p class="product-price">
                            <span class="product-price-text">¥</span>
                            {{ number_format($item->price) }}
                        </p>
                    </div>
                </div>
                <p class="product-border-line"></p>
            </div>
            <div class="payment-section">
                <div class="product-group">
                    <label class="form-label" for="payment_method">支払い方法</label>
                    <div class="select-wrapper">
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="" selected disabled>選択してください</option>
                            <option value="convenience">コンビニ払い</option>
                            <option value="credit">カード支払い</option>
                        </select>
                    </div>
                    <p class="form__error">
                        @error('payment_method')
                            {{ $message }}
                        @enderror
                    </p>
                </div>
                <p class="product-border-line"></p>
            </div>
            <div class="delivery-section">
                <div class="product-group delivery-group">
                    <div class="delivery-header">
                        <p class="form-label">
                            配送先
                        </p>
                        <a href="/purchase/address/{{ $item->id }}" class="change-link link">変更する</a>
                    </div>
                    <div class="delivery-address">
                        <p class="postal-code">
                            〒
                            {{ substr($defaultAddress['postal_code'], 0, 3) }}-{{ substr($defaultAddress['postal_code'], 3) }}
                        </p>
                        <p class="address-text">
                            {{ $defaultAddress['address'] }}
                        </p>
                    </div>
                </div>
                <p class="product-border-line"></p>
            </div>
        </div>
        <div class="order-summary-section">
            <div class="order-summary-box">
                <div class="summary-row">
                    <span class="summary-label">
                        商品代金
                    </span>
                    <span class="summary-value summary-price">
                        <span class="summary-price-text">¥</span>
                        {{ number_format($item->price) }}
                    </span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">
                        支払い方法
                    </span>
                    <span class="summary-value" id="payment_method_display">
                    </span>
                </div>
            </div>
            {{-- フラッシュメッセージの表示 --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if (!is_null($item->buyer_id))
                <button type="button" class="purchase-button sold-button" disabled>SOLD OUT</button>
            @else
                <button type="button" id="purchase_button" class="purchase-button">購入する</button>
            @endif
        </div>
    </div>
@endsection
