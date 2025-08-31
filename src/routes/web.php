<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\FleamarketController;
use App\Http\Controllers\ItemController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 認証関連
//ユーザー登録
Route::get('/register', function () {
    return view('register');
})->name('register');
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// メール認証関連(user)
Route::get('/email/verify', function () {
    return view('verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile')->with('success', 'メール認証が完了しました。');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// メール認証誘導画面
Route::get('/email/verification-notice', function () {
    return view('verify-email');
})->middleware('auth')->name('verification.notice.page');


// プロフィール画面
Route::get('/mypage', [FleamarketController::class, 'showMypage'])->middleware(['auth'])->name('mypage');

// プロフィール設定画面
Route::get('/mypage/profile', [FleamarketController::class, 'showProfile'])->middleware(['auth'])->name('mypage.profile');

// プロフィール更新
Route::post('/mypage/profile', [FleamarketController::class, 'updateProfile'])->middleware(['auth'])->name('mypage.profile.update');

// 商品一覧画面（トップ）
Route::get('/', [ItemController::class, 'index'])->name('top');

// 商品詳細画面
Route::get('/item/{item_id}', [FleamarketController::class, 'showItem'])->name('item.detail');

// 商品詳細画面でのコメント投稿
Route::post('/item/{item_id}/comment', [FleamarketController::class, 'storeComment'])->middleware(['auth'])->name('item.comment');

// 商品出品画面
Route::get('/sell', [FleamarketController::class, 'showSell'])->middleware(['auth'])->name('sell');
Route::post('/sell', [FleamarketController::class, 'storeItem'])->middleware(['auth'])->name('sell.store');

// 商品購入画面
Route::get('/purchase/{item_id}', [FleamarketController::class, 'showPurchase'])->middleware(['auth'])->name('purchase');
Route::post('/purchase/{item_id}', [FleamarketController::class, 'processPurchase'])->middleware(['auth'])->name('purchase.process');

// 送付先住所変更画面
Route::get('/purchase/address/{item_id}', [FleamarketController::class, 'showAddress'])->middleware(['auth'])->name('purchase.address');
Route::post('/purchase/address/{item_id}', [FleamarketController::class, 'updateAddress'])->middleware(['auth'])->name('purchase.address.update');