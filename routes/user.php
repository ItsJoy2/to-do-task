<?php

use App\Http\Controllers\EmailController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\TodoController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->middleware('auth')->group(function () {

    Route::get('dashboard', [TodoController::class, 'index'])->name('user.dashboard');
    Route::post('email/verification-notification',[EmailController::class,'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}',[EmailController::class,'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('task/store', [TodoController::class, 'store'])->name('user.todos.store');
    Route::post('task/{id}/toggle', [TodoController::class, 'toggle'])->name('user.todos.toggle');
    Route::get('task/history', [TodoController::class, 'allTodos'])->name('user.todos.history');
    Route::put('/task/{id}', [TodoController::class, 'update'])->name('user.todos.update');

    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');



    //profile
    Route::get('profile', [AuthController::class, 'profileEdit'])->name('user.profile');
    Route::post('profile', [AuthController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('user.changePassword');

    //todos
    Route::get('todos', [TodoController::class, 'index'])->name('user.todos');
    Route::post('todos', [TodoController::class, 'store'])->name('user.todos.store');
    Route::post('todos/{id}/toggle', [TodoController::class, 'toggle'])->name('user.todos.toggle');
    Route::get('todos/summary', [TodoController::class, 'summary'])->name('user.todos.summary');

});
