<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AddressController;
// use App\Http\Controllers\UserController;
use App\Http\Controllers\LogoutController;

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
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile/edit', [UserController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [UserController::class, 'update'])->name('mypage.profile.update');
});
Route::post('/logout', LogoutController::class)->name('logout');
