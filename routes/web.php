<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

Route::get('/', function () {
    return view('index');
});

// Test endpoint to verify routing works
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Routes are working']);
});

// API routes - direct definition without middleware grouping
Route::post('/ask', [AIController::class, 'ask']);
Route::post('/summarize', [AIController::class, 'summarize']);
Route::post('/eli5', [AIController::class, 'eli5']);
Route::post('/explain-code', [AIController::class, 'explainCode']);
Route::post('/code', [AIController::class, 'explainCode']);
Route::get('/history', [AIController::class, 'history']);
Route::delete('/history/{id}', [AIController::class, 'delete']);