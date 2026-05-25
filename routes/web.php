<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\Backend\FamilyController;
use App\Http\Controllers\Backend\IncomeController;
use App\Http\Controllers\Backend\MemberFinancialReportController;
use App\Http\Controllers\Backend\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
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
    
    // User Management Routes
    Route::controller(UsersController::class)->group(function () {
        Route::get('/all/users', 'AllUsers')->name('all.users');
        Route::get('/add/user', 'AddUser')->name('add.user');
        Route::post('/store/user', 'StoreUser')->name('store.user');
        Route::get('/edit/users/{id}', 'EditUsers')->name('edit.users');
        Route::post('/update/users',  'UpdateUsers')->name('update.users');
        Route::get('/delete/user/{id}', 'DeleteUser')->name('delete.user');
        Route::get('/users/details/{id}', 'UsersDetails')->name('users.details');
    });

    Route::controller(FamilyController::class)->group(function () {
        Route::get('/all/users/family/{id}', 'AllUsersFamily')->name('all.users.family');
        Route::get('/add/users/family/{id}', 'AddUsersFamily')->name('add.users.family');
        Route::post('/store/users/family', 'StoreUsersFamily')->name('store.users.family');
        Route::get('/edit/users/family/{id}', 'EditUsersFamily')->name('edit.users.family');
        Route::post('/update/users/family', 'UpdateUsersFamily')->name('update.users.family');
        Route::get('/delete/users/family/{id}', 'DeleteUsersFamily')->name('delete.users.family');
    });


    Route::controller(CategoryController::class)->group(function () {
        Route::get('/all/category', 'AllCategory')->name('all.category');
        Route::get('/add/category', 'AddCategory')->name('add.category');
        Route::post('/store/category', 'StoreCategory')->name('store.category');
        Route::get('/edit/category/{id}', 'EditCategory')->name('edit.category');
        Route::post('/update/category',  'UpdateCategory')->name('update.category');
        Route::get('/delete/category/{id}', 'DeleteCategory')->name('delete.category');
    });


        Route::controller(IncomeController::class)->group(function () {
        Route::get('/all/income', 'AllIncome')->name('all.income');
        Route::get('/add/income', 'AddIncome')->name('add.income');
        Route::post('/store/income', 'StoreIncome')->name('store.income');
        Route::get('/edit/income/{id}', 'EditIncome')->name('edit.income');
        Route::post('/update/income',  'UpdateIncome')->name('update.income');
        Route::get('/delete/income/{id}', 'DeleteIncome')->name('delete.income');
        Route::get('/undeposited', 'UndepositedIncome')->name('undeposited.income');
        Route::post('/income/transfer/{id}', 'TransferIncome')->name('income.transfer');
    });

    
        Route::controller(ExpenseController::class)->group(function () {
        Route::get('/all/expense', 'AllExpense')->name('all.expense');
        Route::get('/add/expense', 'AddExpense')->name('add.expense');
        Route::post('/store/expense', 'StoreExpense')->name('store.expense');
        Route::get('/edit/expense/{id}', 'EditExpense')->name('edit.expense');
        Route::post('/update/expense',  'UpdateExpense')->name('update.expense');
        Route::get('/delete/expense/{id}', 'DeleteExpense')->name('delete.expense');
    });

    Route::controller(MemberFinancialReportController::class)->group(function () {
        Route::get('/all/member/financial/report', 'AllMemberFinancialReport')->name('all.member.financial.report');
        Route::get('/add/financial/report', 'AddFinancialReport')->name('add.financial.report');
        Route::post('/store/financial/report', 'StoreFinancialReport')->name('store.financial.report');
        Route::get('/edit/financial/report/{id}', 'EditFinancialReport')->name('edit.financial.report');
        Route::post('/update/financial/report',  'UpdateFinancialReport')->name('update.financial.report');
        Route::get('/delete/financial/report/{id}', 'DeleteFinancialReport')->name('delete.financial.report');
    });




});

//End Admin  Route 









Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
  