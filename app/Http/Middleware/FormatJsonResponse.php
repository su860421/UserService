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

            $message = $data['message'] ?? match ($statusCode) {
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

            // 支援 data 或 result 欄位
            $result = null;
            if (isset($data['data'])) {
                $result = $data['data'];
            } elseif (isset($data['result'])) {
                $result = $data['result'];
            } elseif (is_array($data) && !isset($data['status'])) {
                $result = $data;
            } else {
                $result = $data;
            }

            // 組合標準格式
            $formattedResponse = [
                'status' => $status,
                'statusCode' => $statusCode,
                'message' => $message,
                'result' => $result,
                'timestamp' => now()->timestamp,
            ];

            // 保留 Controller 自訂的欄位（如 success, error...），但排除 data 欄位避免重複
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (!in_array($key, ['status', 'statusCode', 'message', 'result', 'timestamp', 'data'])) {
                        $formattedResponse[$key] = $value;
                    }
                }
            }

            // 特殊處理驗證錯誤
            if ($statusCode === 422 && is_array($result) && isset($result['errors'])) {
                $formattedResponse['errors'] = $result['errors'];
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
