<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActualController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\CostCentreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TutorialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/forecast', [DashboardController::class, 'forecast'])->name('forecast');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('cost-centres', CostCentreController::class);
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile.page');

    Route::middleware(['role:admin,finance'])->group(function () {
        Route::resource('accounts', AccountController::class);
        Route::resource('budgets', BudgetController::class);
        Route::post('/budgets/{budget}/lines', [BudgetController::class, 'lines'])->name('budgets.lines');
        Route::get('/import/actuals', [ImportController::class, 'showImportForm'])->name('import.actuals');
        Route::post('/import/actuals', [ImportController::class, 'importActuals']);
        Route::get('/import/actuals/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
    });

    Route::resource('actuals', ActualController::class)->except(['destroy']);
    Route::post('/actuals/{actual}/details', [ActualController::class, 'addDetail'])->name('actuals.details');

    Route::middleware(['role:admin'])->delete('/actuals/{actual}', [ActualController::class, 'destroy'])->name('actuals.destroy');

    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts');
    Route::post('/alerts/generate', [AlertController::class, 'generate'])->name('alerts.generate');
    Route::post('/alerts/{alert}/mark-read', [AlertController::class, 'markRead'])->name('alerts.markRead');

    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');
    Route::get('/analysis/compare', [AnalysisController::class, 'compare'])->name('analysis.compare');
    Route::get('/charts', [ChartsController::class, 'index'])->name('charts');
    Route::get('/tutorial', [TutorialController::class, 'index'])->name('tutorial');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
