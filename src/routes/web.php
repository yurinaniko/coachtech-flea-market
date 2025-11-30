<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::resource('items', ItemController::class);
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');
Route::get('/items/sell', [ItemController::class, 'sell'])->name('items.sell')->middleware('auth');

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index')->middleware('auth');

Route::get('/mypage/profile', [MypageController::class, 'profile'])
        ->name('mypage.profile')
        ->middleware('auth');

Route::post('/favorite/{item}', [ItemController::class, 'favorite'])->name('favorite');
Route::delete('/favorite/{item}', [ItemController::class, 'unfavorite'])->name('unfavorite');