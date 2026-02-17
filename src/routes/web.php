<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/tab/{type}', [ItemController::class, 'fetchTabItems'])->name('items.tab');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

Route::get('/sell', [ItemController::class, 'create'])->middleware('auth')->name('items.create');
Route::post('/sell', [ItemController::class, 'store'])->middleware('auth')->name('items.store');
Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->middleware('auth')->name('purchase.create');
Route::middleware('auth')->post('/items/{item}/favorite', [FavoriteController::class, 'toggle'])->name('favorite.toggle');
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');
});
Route::put('/address', [AddressController::class, 'saveAddress'])->middleware('auth')->name('user.address.save');
Route::get('/purchase/address/{item}', [AddressController::class, 'update'])->middleware('auth')->name('address.update');
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->middleware('auth')->name('purchase.store');
Route::get('purchase/{item}/konbini/return', [PurchaseController::class, 'konbiniReturn'])->name('purchase.return');
Route::middleware('auth','verified')->group(function () {
    Route::get('/mypage', [UserController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile_edit', [UserController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [UserController::class, 'update'])->name('mypage.profile.update');
    Route::get('/mypage/items/{tabType}', [UserController::class, 'getTabItems'])->name('mypage.items.tab');
    Route::get('/chat/{purchase}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{purchase}', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/purchase/{purchase}/complete',[PurchaseController::class, 'complete'])->name('purchase.complete');
    Route::put('/messages/{message}', [ChatController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [ChatController::class, 'destroy'])->name('messages.destroy');
});
Route::post('/logout', LogoutController::class)->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile/setup', [UserController::class, 'setupProfile'])->name('profile.setup');
});

Route::get('/email/verify', function () {
    return view('verification.notice');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect(route('profile.setup'));
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//test用
Route::get('/items/recommended', [ItemController::class, 'recommended']);
Route::get('/items/recommended/search', [ItemController::class, 'recommendedSearch']);