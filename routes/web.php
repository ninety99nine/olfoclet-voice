<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\TranscriptionController;

Route::get('/transcribe', [TranscriptionController::class, 'form'])->name('transcribe.form');
Route::post('/transcribe', [TranscriptionController::class, 'submit'])->name('transcribe.submit');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
