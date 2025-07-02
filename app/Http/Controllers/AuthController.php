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
            $user = $this->authService->register($request->validated());
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => __('register-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('register-failed'), 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => __('login-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('login-failed'), 401);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return response()->json([
                'success' => true,
                'message' => __('logout-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('logout-failed'), 400);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->validate(['refresh_token' => 'required|string']);
        try {
            $result = $this->authService->refresh($request->refresh_token);
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => __('token-refresh-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('token-refresh-failed'), 401);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $user = $this->authService->me();
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-user-info-failed'), 401);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->forgotPassword($request->email);
            return response()->json([
                'success' => true,
                'message' => __('forgot-password-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('forgot-password-failed'), 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request->token, $request->password);
            return response()->json([
                'success' => true,
                'message' => __('reset-password-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('reset-password-failed'), 400);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->changePassword($request->current_password, $request->new_password);
            return response()->json([
                'success' => true,
                'message' => __('change-password-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('change-password-failed'), 400);
        }
    }

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        try {
            $this->authService->verifyEmail($id, $hash);
            return response()->json([
                'success' => true,
                'message' => __('email-verified-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('email-verified-failed'), 400);
        }
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        try {
            $this->authService->resendVerificationEmail($request->email);
            return response()->json([
                'success' => true,
                'message' => __('verification-email-sent')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('verification-email-send-failed'), 400);
        }
    }
}
