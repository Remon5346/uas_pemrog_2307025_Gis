<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceController;

// Halaman Utama
Route::get('/', [PlaceController::class, 'index'])->name('home');

// Simpan Data (Create)
Route::post('/save-place', [PlaceController::class, 'store'])->name('place.store');

// Update Data (Update)
Route::put('/update-place/{id}', [PlaceController::class, 'update'])->name('place.update');

// Hapus Data (Delete)
Route::delete('/delete-place/{id}', [PlaceController::class, 'destroy'])->name('place.destroy');
Auth::routes();

// Arahkan dashboard /home ke PlaceController agar data peta muncul
Route::get('/home', [App\Http\Controllers\PlaceController::class, 'index'])->name('home');
