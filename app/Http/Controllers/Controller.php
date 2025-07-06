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
use JoeSu\LaravelScaffold\Exceptions\RepositoryException;

abstract class Controller
{
    /**
     * 統一錯誤處理方法
     */
    protected function handleException(Exception $e, string $defaultMessage, int $defaultCode = 500): JsonResponse
    {
        $statusCode = $defaultCode;
        $message = $defaultMessage;
        $errorType = 'system_error';
        $errorCode = 'UNKNOWN_ERROR';

        // 根據異常類型調整狀態碼、訊息和錯誤類型
        if ($e instanceof RepositoryException) {
            $statusCode = $e->getCode() ?: 500;
            $message = $e->getMessage() ?: $defaultMessage;
            $errorType = 'repository_error';
            $errorCode = 'REPOSITORY_ERROR';
        } elseif ($e instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = __('error-resource-not-found');
            $errorType = 'resource_not_found';
            $errorCode = 'RESOURCE_NOT_FOUND';
        } elseif ($e instanceof ValidationException) {
            $statusCode = 422;
            $message = __('error-validation');
            $errorType = 'validation_error';
            $errorCode = 'VALIDATION_ERROR';
        } elseif ($e instanceof JWTException) {
            $statusCode = 401;
            $message = __('error-jwt');
            $errorType = 'authentication_error';
            $errorCode = 'AUTHENTICATION_ERROR';
        } elseif ($e instanceof QueryException) {
            $statusCode = 500;
            $message = __('error-db');
            $errorType = 'database_error';
            $errorCode = 'DATABASE_ERROR';
        } elseif ($e instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = __('error-http-not-found');
            $errorType = 'route_not_found';
            $errorCode = 'ROUTE_NOT_FOUND';
        } elseif ($e instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = __('error-http-forbidden');
            $errorType = 'permission_denied';
            $errorCode = 'PERMISSION_DENIED';
        } elseif ($e->getCode() >= 400 && $e->getCode() < 600) {
            $statusCode = $e->getCode();
            $errorCode = 'HTTP_ERROR_' . $statusCode;
        }

        Log::error('API 錯誤', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'status_code' => $statusCode,
            'error_type' => $errorType,
            'error_code' => $errorCode,
            'exception_class' => get_class($e)
        ]);

        return response()->json([
            'status' => 'error',
            'statusCode' => $statusCode,
            'message' => $message,
            'error' => [
                'type' => $errorType,
                'code' => $errorCode,
                'details' => config('app.debug') ? $e->getMessage() : null,
                'timestamp' => time()
            ],
            'result' => null
        ], $statusCode);
    }
}
