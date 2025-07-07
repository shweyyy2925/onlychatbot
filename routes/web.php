<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminPdfController;


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::get('/', fn() => view('chat'));
//Route::post('/chat', [ChatController::class, 'ask']);
Route::post('/admin/upload-pdf', [AdminPdfController::class, 'upload']);

Route::get('/embed/{id}', function ($id) {
    return view('embed', ['chatbot_id' => $id]);
});

Route::get('/chat-ui', function () {
    return view('chat'); // your existing chat.blade.php
});

require __DIR__.'/auth.php';
