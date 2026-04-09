<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActualController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CostCentreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    Route::resource('cost-centres', CostCentreController::class);
    Route::resource('accounts', AccountController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('actuals', ActualController::class);

    Route::post('/budgets/{budget}/lines', [BudgetController::class, 'lines'])->name('budgets.lines');
    Route::post('/actuals/{actual}/details', [ActualController::class, 'addDetail'])->name('actuals.details');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
