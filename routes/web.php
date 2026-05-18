<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

///User Route
Route::middleware(['auth' ,IsUser::class ])->group(function () {

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


});

//End User Route

///Admin Route
Route::prefix('admin')->middleware(['auth' ,IsAdmin::class ])->group(function () {


Route::get('/dashboard', function () {
    return view('admin.index');
})->name('admin.dashboard');

Route::get('/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
Route::get('/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
Route::post('/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
Route::get('/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
Route::post('/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');

});

//End Admin  Route 









Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
  