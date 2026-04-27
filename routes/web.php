<?php

use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\GeneralSettingsController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('user.dashboard');
});

Route::get('admin/dashboard',[TodoController::class,'index'])->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::prefix('admin')->middleware('auth')->group(function () {

    //all user
    Route::get('users', [UsersController::class, 'index'])->name('admin.users.index');
    Route::put('users/update', [UsersController::class, 'update'])->name('admin.users.update');
    Route::get('users/{id}', [UsersController::class, 'show'])->name('admin.users.show');
    Route::post('users/wallet-update', [UsersController::class, 'updateWallet'])->name('admin.users.wallet.update');

    Route::get('all-task', [TodoController::class, 'list'])->name('admin.tasks.list');
    Route::resource('tasks', TodoController::class)->names([
    'create'  => 'admin.tasks.add',
    'store'   => 'admin.tasks.store',
    'show'    => 'admin.tasks.view',
    'edit'    => 'admin.tasks.edit',
    'update'  => 'admin.tasks.update',
    'destroy' => 'admin.tasks.delete',
]);


    // General Settings
    Route::get('general-settings', [GeneralSettingsController::class, 'index'])->name('admin.general.settings');
    Route::post('general-settings', [GeneralSettingsController::class, 'update'])->name('admin.general.settings.update');

    //profile settings

    Route::get('profile', [AuthenticatedSessionController::class, 'profileEdit'])->name('admin.profile.edit');
    Route::post('profile', [AuthenticatedSessionController::class, 'profileUpdate'])->name('admin.profile.update');


});

Route::get('check',function(){
    return \Carbon\Carbon::now();
});

require __DIR__.'/auth.php';
require __DIR__.'/auths.php';
require __DIR__.'/user.php';
