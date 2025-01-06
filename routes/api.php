<?php

use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
  Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])
    ->name('api.dashboard.chart-data');

  Route::get('/dashboard/live-feed', [DashboardController::class, 'getLiveFeed'])
    ->name('api.dashboard.live-feed');
});
