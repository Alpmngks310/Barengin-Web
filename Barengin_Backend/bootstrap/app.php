<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dikirim tidak valid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Silakan login kembali.',
                    'data' => null,
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu tidak memiliki izin untuk melakukan tindakan ini.',
                    'data' => null,
                ], 403);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Kamu tidak memiliki izin untuk melakukan tindakan ini.',
                    'data' => null,
                ], 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau sudah tidak tersedia.',
                    'data' => null,
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau sudah tidak tersedia.',
                    'data' => null,
                ], 404);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage() ?: 'Terjadi kesalahan pada request.';
                if (str_contains($message, 'No query results for model') || str_contains($message, 'App\\Models\\')) {
                    $message = 'Data tidak ditemukan atau sudah tidak tersedia.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => null,
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : 500;
                $message = $status === 500
                    ? (config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server.')
                    : ($e->getMessage() ?: 'Terjadi kesalahan pada request.');

                if (str_contains($message, 'No query results for model') || str_contains($message, 'App\\Models\\')) {
                    $message = 'Data tidak ditemukan atau sudah tidak tersedia.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => null,
                ], $status);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dikirim tidak valid.',
                    'data' => null,
                    'errors' => $e->errors(),
                ], 422);
            }
        });
    })
    ->create();
