<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormatJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->original;
            $statusCode = $response->getStatusCode();

            // 如果已經格式化過，直接回傳
            if (is_array($data) && isset($data['status'], $data['statusCode'], $data['message'])) {
                return $response;
            }

            $status = match ($statusCode) {
                200, 201, 204 => 'success',
                300, 301, 302 => 'redirect',
                400 => 'bad_request',
                401 => 'unauthorized',
                403 => 'forbidden',
                404 => 'not_found',
                422 => 'validation_error',
                429 => 'too_many_requests',
                500, 502, 503 => 'error',
                default => 'unknown',
            };

            $message = match ($statusCode) {
                200 => 'Resource retrieved successfully',
                201 => 'Resource created successfully',
                204 => 'No content',
                300, 301, 302 => 'Redirect',
                400 => 'Bad request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Resource not found',
                422 => 'Validation failed',
                429 => 'Too many requests',
                500 => 'Internal server error',
                502 => 'Bad gateway',
                503 => 'Service unavailable',
                default => 'Unknown error',
            };

            // 允許 Controller 自訂狀態和訊息
            if (is_array($data)) {
                $status = $data['status'] ?? $status;
                $message = $data['message'] ?? $message;
            }

            // 處理不同資料結構
            if (isset($data['result'])) {
                $result = $data['result'];
            } elseif (is_array($data) && !isset($data['status'])) {
                $result = $data;
            } else {
                $result = $data;
            }

            // 特殊處理驗證錯誤
            if ($statusCode === 422 && is_array($result) && isset($result['errors'])) {
                $formattedResponse = [
                    'status' => $status,
                    'statusCode' => $statusCode,
                    'message' => $message,
                    'errors' => $result['errors'],
                    'timestamp' => now()->timestamp,
                ];
            } else {
                $formattedResponse = [
                    'status' => $status,
                    'statusCode' => $statusCode,
                    'message' => $message,
                    'result' => $result,
                    'timestamp' => now()->timestamp,
                ];
            }

            // 除錯模式下的額外資訊
            if (config('app.debug') && $statusCode >= 500 && is_array($result)) {
                $formattedResponse['debug'] = $result;
            }

            return response()->json($formattedResponse, $statusCode);
        }

        return $response;
    }
}
