<?php

use App\Http\Controllers\Frontend\AnnouncementController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('public.home');
Route::get('/berita', [NewsController::class, 'index'])->name('public.news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('public.news.show');
Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('public.announcements.index');
Route::get('/pengumuman/{slug}', [AnnouncementController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('public.announcements.show');
Route::get('/galeri', [GalleryController::class, 'index'])->name('public.galleries.index');
Route::get('/galeri/{slug}', [GalleryController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('public.galleries.show');
