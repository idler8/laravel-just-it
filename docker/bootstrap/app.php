<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::view('/', 'welcome');
            Route::middleware('api')
                ->namespace('Justit')
                ->prefix('r')
                ->group(function () {
                    Route::get("{name}/{pre_page}/{page}", "Controller@paginate");
                    Route::get("{name}", "Controller@index");

                    Route::post("{name}", "Controller@store");
                    Route::delete("{name}/{id}", "Controller@destroy");
                    Route::put("{name}/{id}", "Controller@update");
                    Route::get("{name}/{id}", "Controller@show");
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {})->create();
