<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminPdfController;

/*
|--------------------------------------------------------------------------
| Chatbot Routes
|--------------------------------------------------------------------------
*/

// Homepage to show all chats (optional)
Route::get('/', [ChatController::class, 'index']);

// This route handles chat messages (POST only)
Route::post('/chat', [ChatController::class, 'ask'])->name('chat.ask');

// Optional: Allow OPTIONS preflight request to prevent CORS issues
Route::options('/chat', function () {
    return response()->json([], 200);
});

/*
|--------------------------------------------------------------------------
| Chat UI Route (Frontend)
|--------------------------------------------------------------------------
*/
// This loads the chat.blade.php interface
Route::get('/chat-ui', function () {
    return view('chat');
});

/*
|--------------------------------------------------------------------------
| Admin PDF Upload Route (POST)
|--------------------------------------------------------------------------
*/
Route::post('/admin/upload-pdf', [AdminPdfController::class, 'upload']);

/*
|--------------------------------------------------------------------------
| Embed Chat (Optional Public View)
|--------------------------------------------------------------------------
*/
Route::get('/embed/{id}', function ($id) {
    return view('embed', ['chatbot_id' => $id]);
});
Route::middleware(['web'])->post('/api/ask', [ChatController::class, 'askPublic']);
