<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class Controller
{
    /**
     * 統一錯誤處理方法
     */
    protected function handleException(Exception $e, string $defaultMessage, int $defaultCode = 500): JsonResponse
    {
        $statusCode = $defaultCode;
        $message = $defaultMessage;

        // 根據異常類型調整狀態碼和訊息（多語系）
        if ($e instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = __('error-resource-not-found');
        } elseif ($e instanceof ValidationException) {
            $statusCode = 422;
            $message = __('error-validation');
        } elseif ($e instanceof JWTException) {
            $statusCode = 401;
            $message = __('error-jwt');
        } elseif ($e instanceof QueryException) {
            $statusCode = 500;
            $message = __('error-db');
        } elseif ($e instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = __('error-http-not-found');
        } elseif ($e instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = __('error-http-forbidden');
        } elseif ($e->getCode() >= 400 && $e->getCode() < 600) {
            $statusCode = $e->getCode();
        }

        Log::error('API 錯誤', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'status_code' => $statusCode,
            'exception_class' => get_class($e)
        ]);

        return response()->json([
            'message' => $message,
            'error' => config('app.debug') ? $e->getMessage() : null
        ], $statusCode);
    }
}
