<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::resource('items', ItemController::class);
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');
Route::get('/items/sell', [ItemController::class, 'sell'])->name('items.sell')->middleware('auth');
Route::post('/items/{item}/comment', [CommentController::class, 'store'])
    ->name('comment.store')
    ->middleware('auth');

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// マイページトップ（商品一覧）
Route::get('/mypage', [MypageController::class, 'index'])
    ->name('mypage.index')
    ->middleware('auth');

// プロフィール表示
Route::get('/mypage/profile', [MypageController::class, 'profile'])
    ->name('mypage.profile')
    ->middleware('auth');

// 初回プロフィール設定（ログイン直後に表示）
Route::middleware('auth')->group(function () {

    Route::get('/mypage/profile/create', [MypageController::class, 'create'])
        ->name('mypage.profile.create');

    Route::post('/mypage/profile/store', [MypageController::class, 'store'])
        ->name('mypage.profile.store');

    // 編集
    Route::get('/mypage/profile/edit', [MypageController::class, 'edit'])
        ->name('mypage.profile.edit');

    Route::put('/mypage/profile/update', [MypageController::class, 'update'])
        ->name('mypage.profile.update');
});

Route::post('/favorite/{item}', [ItemController::class, 'favorite'])->name('favorite');
Route::delete('/favorite/{item}', [ItemController::class, 'unfavorite'])->name('unfavorite');

Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');