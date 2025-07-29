<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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
                    $e instanceof ValidationException => response()->json([
                        'success' => 0,
                        'message' => $e->getMessage(),
                        'result' => ['errors' => $e->errors()],
                    ], Response::HTTP_UNPROCESSABLE_ENTITY),

                    $e instanceof Exception => response()->json([
                        'success' => 0,
                        'message' => $e->getMessage(),
                        'result' => 'Eexception',
                    ], 400),
                    $e instanceof Throwable => response()->json([
                        'success' => 0,
                        'message' => $e->getMessage(),
                        'result' => 'Throwable',
                    ], 400),
                    default => response()->json([
                        'success' => 0,
                        'message' => 'Something bad happend',
                        'result' => 'Error',
                    ], 500),
                };
            }
        });
    })->create();
