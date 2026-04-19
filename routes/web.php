<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\Admin\InstansiController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('instansi', InstansiController::class);
    Route::resource('user', UserController::class);
});

use App\Http\Controllers\Sekolah\PengajuanController;

Route::middleware(['auth', 'role:sekolah'])->prefix('sekolah')->name('sekolah.')->group(function () {
    Route::resource('pengajuan', PengajuanController::class);
    
    // Custom route untuk form upload dokumen
    Route::post('pengajuan/{id}/upload-dokumen', [PengajuanController::class, 'uploadDokumen'])->name('pengajuan.upload');
    
    // Custom route untuk men-submit (mengubah status Draft -> Diajukan)
    Route::post('pengajuan/{id}/submit', [PengajuanController::class, 'submitPengajuan'])->name('pengajuan.submit');
});

use App\Http\Controllers\Verifikator\VerifikasiController;

Route::middleware(['auth', 'role:verifikator'])->prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('pengajuan', [VerifikasiController::class, 'index'])->name('pengajuan.index');
    Route::get('pengajuan/{id}', [VerifikasiController::class, 'show'])->name('pengajuan.show');
    Route::post('pengajuan/{id}/proses', [VerifikasiController::class, 'prosesVerifikasi'])->name('pengajuan.proses');
});

require __DIR__.'/auth.php';
