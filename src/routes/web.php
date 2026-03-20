<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
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
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('mypage.index')
        : redirect()->route('items.index');
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()
        ->route('profile.create')
        ->with('verified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:10,1'])->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'index'])
        ->name('mypage.index');
    Route::get('/mypage/profile', [MypageController::class, 'profile'])
        ->name('mypage.profile');
    Route::get('mypage/address/edit', [AddressController::class, 'edit'])
        ->name('mypage.address.edit');
    Route::put('mypage/address/update', [AddressController::class, 'update'])
        ->name('mypage.address.update');
    Route::get('/items/sell', [ItemController::class, 'sell'])
        ->name('items.item-sell');
    Route::post('/items/store', [ItemController::class, 'store'])
        ->name('items.store');
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'toggle'])
        ->name('favorite.toggle');
    Route::post('/items/{item}/comment', [CommentController::class, 'store'])
        ->name('comment.store');
    Route::get('/purchase/checkout', [PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');
    Route::get('/purchase/result', [PurchaseController::class, 'result'])
        ->name('purchase.result');
    Route::get('/purchase/{item}', [PurchaseController::class, 'index'])
        ->whereNumber('item')
        ->name('purchase.index');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
        ->whereNumber('item')
        ->name('purchase.store');
});

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{id}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
