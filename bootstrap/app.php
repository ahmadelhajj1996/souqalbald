<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Exception $e, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                return match (true) {
                    $e instanceof Exception => response()->json([
                        'success' => 0,
                        'result' => 'Eexception',
                        'message' => $e->getMessage(),
                    ], 400),
                    $e instanceof Throwable => response()->json([
                        'success' => 0,
                        'result' => 'Throwable',
                        'message' => $e->getMessage(),
                    ], 400),
                    default => response()->json([
                        'success' => 0,
                        'result' => 'Error',
                        'message' => 'Something bad happend',
                    ], 500),
                };
            }
        });
    })->create();
