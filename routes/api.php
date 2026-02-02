<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Client API routes with api. prefix to avoid conflicts with web routes
    Route::get('clients', [ClientController::class, 'index'])->name('api.clients.index');
    Route::post('clients', [ClientController::class, 'store'])->name('api.clients.store');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('api.clients.show');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('api.clients.update');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('api.clients.destroy');
    
    // Document API routes (no apiResource to avoid conflicts with web routes)
    Route::get('clients/{client}/documents', [DocumentController::class, 'index'])->name('api.clients.documents');
    Route::post('clients/{client}/documents', [DocumentController::class, 'store'])->name('api.documents.store');
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('api.documents.show');
    Route::put('documents/{document}', [DocumentController::class, 'update'])->name('api.documents.update');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('api.documents.destroy');
    
    // Upload API routes
    Route::get('documents/{document}/uploads', [UploadController::class, 'index'])->name('api.uploads.index');
    Route::post('documents/{document}/uploads', [UploadController::class, 'store'])->name('api.uploads.store');
    Route::get('uploads/{upload}', [UploadController::class, 'show'])->name('api.uploads.show');
    Route::put('uploads/{upload}', [UploadController::class, 'update'])->name('api.uploads.update');
    Route::delete('uploads/{upload}', [UploadController::class, 'destroy'])->name('api.uploads.destroy');
});
