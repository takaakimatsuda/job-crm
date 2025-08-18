<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 追加で使う例外クラス
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // 必要に応じて他のミドルウェアをここへ
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 503: 外部サービス停止・APIキー未設定など（ServiceUnavailable）
        $exceptions->render(function (ServiceUnavailableHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code'    => 'OPENAI_ERROR', // フロント案1で使用
                    'message' => $e->getMessage() ?: 'Service unavailable',
                ], 503);
            }
        });

        // 429: レート制限（Retry-After 付き）
        $exceptions->render(function (TooManyRequestsHttpException $e, $request) {
            if ($request->expectsJson()) {
                // 例外が持つヘッダを優先（無ければ 60 秒）
                $headers = method_exists($e, 'getHeaders') ? $e->getHeaders() : [];
                $retry   = (int)($headers['Retry-After'] ?? 60);

                return response()->json([
                    'code'        => 'RATE_LIMITED',
                    'message'     => $e->getMessage() ?: 'Too many requests',
                    'retry_after' => $retry,
                ], 429)->header('Retry-After', $retry);
            }
        });

        // ★ フォールバック: あらゆる HttpException を本来のステータスで JSON 化
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->expectsJson()) {
                $status  = $e->getStatusCode();
                $headers = method_exists($e, 'getHeaders') ? $e->getHeaders() : [];
                // 503 は OPENAI_ERROR に寄せ、それ以外は汎用コード
                $code = $status === 503 ? 'OPENAI_ERROR' : 'HTTP_ERROR';

                return response()->json([
                    'code'    => $code,
                    'message' => $e->getMessage() ?: 'HTTP error',
                ], $status, $headers);
            }
        });
    })
    ->create();
