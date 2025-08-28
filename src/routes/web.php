<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;



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
Route::post('/register', function (App\Http\Requests\RegisterRequest $request) {
    return app(\Laravel\Fortify\Contracts\CreatesNewUsers::class)->create($request->validated());
})->name('register.store');
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', function (App\Http\Requests\LoginRequest $request) {
    // バリデーション済みデータで認証処理を実行
    $credentials = $request->validated();

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        return app(\Laravel\Fortify\Contracts\LoginResponse::class)->toResponse($request);
    }

    return back()->withErrors([
        'email' => 'ログイン情報が登録されていません',
    ]);
})->name('login.store');
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


// プロフィール設定画面
Route::get('/mypage/profile', function () {
    return view('mypage.profile');
})->middleware(['auth'])->name('mypage.profile');

// ホーム
Route::get('/', function () {
    return view('home');
});
