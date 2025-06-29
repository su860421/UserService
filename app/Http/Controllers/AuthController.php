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
            $statusCode = $result['success'] ? 201 : 500;

            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            Log::error('註冊 API 失敗', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => '註冊失敗，請稍後再試'
            ], $statusCode);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            $statusCode = $result['success'] ? 200 : 401;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '登入失敗，請稍後再試'
            ], $statusCode);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $result = $this->authService->logout();
            $statusCode = $result['success'] ? 200 : 500;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '登出失敗，請稍後再試'
            ], $statusCode);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refresh();
            $statusCode = $result['success'] ? 200 : 401;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => 'Token 更新失敗，請稍後再試'
            ], $statusCode);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $result = $this->authService->me();
            $statusCode = $result['success'] ? 200 : 500;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '取得使用者資訊失敗'
            ], $statusCode);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->forgotPassword($request->email);
            $statusCode = $result['success'] ? 200 : 404;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '忘記密碼處理失敗'
            ], $statusCode);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resetPassword($request->token, $request->password);
            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '重設密碼失敗'
            ], $statusCode);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->changePassword($request->current_password, $request->new_password);
            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '修改密碼失敗'
            ], $statusCode);
        }
    }

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        try {
            $result = $this->authService->verifyEmail($id, $hash);
            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '電子郵件驗證失敗'
            ], $statusCode);
        }
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resendVerificationEmail($request->email);
            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '重新發送驗證郵件失敗'
            ], $statusCode);
        }
    }
}
