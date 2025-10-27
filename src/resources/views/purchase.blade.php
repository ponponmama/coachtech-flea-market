{{-- 商品購入画面 - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('js')
    {{-- カスタムセレクトボックスのJavaScript --}}
    <script src="{{ asset('js/custom-select.js') }}"></script>
    {{-- Stripe決済処理用のJavaScript（外部ファイル） --}}
    <script src="{{ asset('js/purchase.js') }}"></script>
@endsection

@section('content')
    <div class="content-container" data-item-id="{{ $item->id }}">
        <form action="{{ route('payment.create-session') }}" method="POST" class="purchase-form">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
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
                        <span class="form-label">支払い方法</span>
                        <div class="select-wrapper">
                            <div class="custom-select" id="custom_payment_select">
                                <div class="custom-select-trigger">
                                    <span class="custom-select-value">選択してください</span>
                                </div>
                                <ul class="custom-select-options">
                                    <li class="custom-option" data-value="convenience">
                                        <span class="check-mark"></span>
                                        <span class="option-text">コンビニ払い</span>
                                    </li>
                                    <li class="custom-option" data-value="credit">
                                        <span class="check-mark"></span>
                                        <span class="option-text">カード支払い</span>
                                    </li>
                                </ul>
                            </div>
                            <!-- フォーム送信用の隠しinput -->
                            <input type="hidden" name="payment_method" id="payment_method" value="">
                            <input type="hidden" name="shipping_address" value="{{ $defaultAddress['address'] ?? '' }}">
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
                            <p class="form-label delivery-label">
                                配送先
                            </p>
                            <a href="/purchase/address/{{ $item->id }}" class="change-link link">変更する</a>
                        </div>
                        <div class="delivery-address">
                            {{-- 住所変更成功メッセージ --}}
                            @if (session('address_updated'))
                                <p class="alert alert-success">
                                    {{ session('address_updated') }}
                                </p>
                            @endif
                            <p class="postal-code">
                                〒
                                {{ substr($defaultAddress['postal_code'], 0, 3) }}-{{ substr($defaultAddress['postal_code'], 3) }}
                            </p>
                            <p class="address-text">
                                {{ $defaultAddress['address'] }}
                            </p>
                        </div>

                        <p class="form__error">
                            @error('shipping_address')
                                {{ $message }}
                            @enderror
                        </p>
                    </div>
                    <p class="product-border-line"></p>
                </div>
            </div>
            <!-- 右側：注文内容・決済情報 -->
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
                    <div id="purchase_button" class="purchase-button">購入する</div>
                @endif
            </div>
        </form>
    </div>
@endsection
