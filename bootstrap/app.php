<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    },
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \App\Http\Middleware\JsonResponse::class, // prevent redirecting
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }
        })->render(function (
            Illuminate\Validation\ValidationException $e
        ) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        })->render(function (
            \Exception $e
        ) {
            // General exceptions need to be logged
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An internal error occurred. See the logs for the details',
            ], 400);
        });
    })->create();
