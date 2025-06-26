<?php

use App\Http\Controllers\Api\User\NotesController;
use Illuminate\Support\Facades\Route;

Route::controller(NotesController::class)->middleware('auth.jwt')->group(function () {

    //user notes route function
    Route::get('/user-notes-list', 'notesList');

    Route::post('/user-notes-create', 'notesCreate');
    Route::get('/user-notes-details/{id}', 'noteDdetails');
    Route::post('/user-notes-update/{id}', 'notesUpdate');
    Route::delete('/user-notes-destroy/{id}', 'notesDestroy');

    // voice notes route function
    Route::post('/user-voice/note-create', 'voiceCreate');
    Route::get('/user-voice/note-details/{id}', 'voiceDetails');
    Route::post('/user-voice/note-update/{id}', 'voiceUpdate');
    Route::delete('/user-voice/note-destroy/{id}', 'voiceDestroy');
});
