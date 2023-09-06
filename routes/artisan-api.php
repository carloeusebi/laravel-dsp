<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::prefix('/artisan')->group(function () {
  Route::post('/refresh-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:cache');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
  });
});
