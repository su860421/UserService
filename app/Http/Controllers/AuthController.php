<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\AuthServiceInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ], 201);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 400;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '註冊失敗，請稍後再試', 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 401;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '登入失敗，請稍後再試', 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $result = $this->authService->logout();

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 401;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '登出失敗，請稍後再試', 500);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refresh();

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 401;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, 'Token 更新失敗，請稍後再試', 401);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $result = $this->authService->me();

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 401;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '取得使用者資訊失敗', 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->forgotPassword($request->email);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 404;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '忘記密碼處理失敗', 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resetPassword($request->token, $request->password);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 400;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '重設密碼失敗', 400);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->changePassword($request->current_password, $request->new_password);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 400;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '修改密碼失敗', 400);
        }
    }

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        try {
            $result = $this->authService->verifyEmail($id, $hash);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 400;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '電子郵件驗證失敗', 400);
        }
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resendVerificationEmail($request->email);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 400;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '重新發送驗證郵件失敗', 400);
        }
    }

    /**
     * 統一錯誤處理方法
     */
    private function handleException(Exception $e, string $defaultMessage, int $defaultCode = 500): JsonResponse
    {
        $statusCode = $defaultCode;
        $message = $defaultMessage;

        // 根據異常類型調整狀態碼和訊息
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $statusCode = 404;
            $message = '資源不存在';
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            $statusCode = 422;
            $message = '驗證失敗';
        } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
            $statusCode = 401;
            $message = 'Token 無效或已過期';
        } elseif ($e instanceof \Illuminate\Database\QueryException) {
            $statusCode = 500;
            $message = '資料庫操作失敗';
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $statusCode = 404;
            $message = '請求的資源不存在';
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            $statusCode = 403;
            $message = '沒有權限訪問此資源';
        } elseif ($e->getCode() >= 400 && $e->getCode() < 600) {
            $statusCode = $e->getCode();
        }

        Log::error('Auth API 錯誤', [
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
