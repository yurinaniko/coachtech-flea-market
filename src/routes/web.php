<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// トップページ：ログインしていたらマイページ / していなければ商品一覧
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('mypage.index')
        : redirect()->route('items.index');
});

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register.form');
Route::post('/register', [AuthController::class, 'register']);

// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])
    ->middleware('auth')
    ->name('verification.notice');

// メール内リンクを踏んだとき
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()
        ->route('profile.create')
        ->with('verified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:1,1'])->name('verification.send');

Route::middleware('auth')->group(function () {
// プロフィール登録
Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');

// プロフィール編集
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/mypage', [MypageController::class, 'index'])
        ->name('mypage.index');

    Route::get('/mypage/profile', [MypageController::class, 'profile'])
        ->name('mypage.profile');

});

// 商品一覧
Route::get('/items', [ItemController::class, 'index'])->name('items.index');

Route::middleware(['auth', 'verified'])->group(function () {

// 出品ページ
Route::get('/items/sell', [ItemController::class, 'sell'])
    ->name('items.item-sell');

// 出品処理
Route::post('/items/store', [ItemController::class, 'store'])
    ->name('items.store');

});

Route::get('mypage/address/edit', [AddressController::class, 'edit'])
    ->name('mypage.address.edit')
    ->middleware(['auth', 'verified']);

Route::put('mypage/address/update', [AddressController::class, 'update'])
    ->name('mypage.address.update')
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    // お気に入り
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'toggle'])
    ->name('favorite.toggle');

    // コメント
    Route::post('/items/{item}/comment', [CommentController::class, 'store'])
    ->name('comment.store');
});

// 商品詳細（最後に置く）
Route::get('/items/{id}', [ItemController::class, 'show'])
    ->name('items.show');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::get('/purchase/success', [PurchaseController::class, 'success'])
    ->name('purchase.success');

Route::get('/purchase/cancel', function () {
    return view('purchase.cancel');
})->name('purchase.cancel');

// 購入
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchase/checkout', [PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');

    Route::get('/purchase/{item}', [PurchaseController::class, 'index'])
    ->name('purchase.index');

    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
    ->name('purchase.store');
});