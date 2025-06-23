<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/Web/web.php',
        api: __DIR__ . '/../routes/Api/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware(['web'])
                ->group(base_path('routes/Web/auth.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/Web/backend.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin/settings')
                ->group(base_path('routes/Web/settings.php'));

            Route::middleware(['api'])
                ->prefix('api')
                ->group(base_path('routes/Api/auth.php'));

            Route::middleware(['api', 'auth.jwt'])
                ->prefix('api')
                ->group(base_path('routes/Api/chat.php'));
        },
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        [
            'prefix'     => 'api',
            'middleware' => [
                'api', 'auth.jwt',
            ],
        ],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'    => AdminMiddleware::class,
            'auth.jwt' => Authenticate::class,
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);
            }
        });
    })->create();
