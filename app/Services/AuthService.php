<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\Exceptions\ServiceException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function register(array $data): User
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->create($data);
            $user->sendEmailVerificationNotification();
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new ServiceException('Invalid credentials', 'invalid-credentials', 401);
        }
        $user = Auth::user();
        if (!$user->is_active) {
            throw new ServiceException('Account is disabled', 'account-disabled', 403);
        }
        if (!$user->email_verified_at) {
            throw new ServiceException('Email not verified', 'email-not-verified', 403);
        }
        return $this->generateTokenResponse($user);
    }

    public function logout(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->invalidateUserToken($user);
        }
    }

    public function refresh(string $refreshToken): array
    {
        $key = 'refresh_token:' . $refreshToken;
        $data = Cache::get($key);
        if (!$data) {
            throw new ServiceException('Refresh token is invalid', 'refresh-token-invalid', 401);
        }
        $user = $this->userRepository->find($data['user_id']);
        if (!$user) {
            Cache::forget($key);
            throw new ServiceException('User not found', 'user-not-found', 404);
        }
        Cache::forget($key);
        return $this->generateTokenResponse($user);
    }

    public function me(): User
    {
        $user = Auth::user();
        if (!$user) {
            throw new ServiceException('User not found', 'user-not-found', 404);
        }
        return $user;
    }

    public function forgotPassword(string $email): void
    {
        DB::beginTransaction();
        try {
            $users = $this->userRepository->index(1, null, 'asc', [], ['*'], [['email', '=', $email]]);
            if ($users->isEmpty()) {
                DB::rollBack();
                throw new ServiceException('User not found', 'user-not-found', 404);
            }
            $user = $users->first();
            if (!$user->email_verified_at) {
                DB::rollBack();
                throw new ServiceException('Email not verified', 'email-not-verified', 403);
            }
            $status = Password::sendResetLink(['email' => $email]);
            if ($status !== Password::RESET_LINK_SENT) {
                DB::rollBack();
                throw new ServiceException('Failed to send reset link', 'forgot-password-failed', 500);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resetPassword(string $token, string $password): void
    {
        DB::beginTransaction();
        try {
            $status = Password::reset(
                ['token' => $token, 'password' => $password],
                function (User $user, string $password) {
                    $this->userRepository->update($user->id, [
                        'password' => Hash::make($password)
                    ]);
                    $this->invalidateUserToken($user);
                }
            );
            if ($status !== Password::PASSWORD_RESET) {
                DB::rollBack();
                throw new ServiceException('Failed to reset password', 'reset-password-failed', 500);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changePassword(string $currentPassword, string $newPassword): void
    {
        $user = Auth::user();
        if (!Hash::check($currentPassword, $user->password)) {
            throw new ServiceException('Current password is wrong', 'current-password-wrong', 400);
        }
        $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword)
        ]);
        $this->invalidateUserToken($user);
    }

    public function verifyEmail(string $id, string $hash): void
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new ServiceException('User not found', 'user-not-found', 404);
        }
        if ($user->email_verified_at) {
            return;
        }
        if (!hash_equals(sha1($user->email), $hash)) {
            throw new ServiceException('Invalid verification link', 'invalid-verification-link', 400);
        }
        $this->userRepository->update($user->id, [
            'email_verified_at' => now()
        ]);
    }

    public function resendVerificationEmail(string $email): void
    {
        $users = $this->userRepository->index(1, null, 'asc', [], ['*'], [['email', '=', $email]]);
        if ($users->isEmpty()) {
            throw new ServiceException('User not found', 'user-not-found', 404);
        }
        $user = $users->first();
        if ($user->email_verified_at) {
            throw new ServiceException('Email already verified', 'email-already-verified', 400);
        }
        $user->sendEmailVerificationNotification();
    }

    private function generateTokenResponse($user): array
    {
        $jwtToken = JWTAuth::fromUser($user);
        $expiresInSeconds = JWTAuth::factory()->getTTL() * 60;
        $expiredAt = now()->addSeconds($expiresInSeconds)->format('Y-m-d H:i:s');

        $jwtKey = 'jwt_' . $user->id;
        $oldJwt = Cache::get($jwtKey);
        if ($oldJwt && JWTAuth::setToken($oldJwt)->check()) {
            JWTAuth::setToken($oldJwt)->invalidate();
        }
        Cache::put($jwtKey, $jwtToken, $expiresInSeconds);

        // 同步產生 refresh token，改用 Cache 儲存
        $refreshToken = Str::random(64);
        $refreshKey = 'refresh_token:' . $refreshToken;
        $refreshExpiredAt = now()->addHours(3);
        Cache::put($refreshKey, [
            'user_id' => $user->id,
            'expires_at' => $refreshExpiredAt->toDateTimeString(),
        ], $refreshExpiredAt);

        return [
            'access_token' => $jwtToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresInSeconds,
            'expired_at' => $expiredAt,
            'refresh_token_expires_at' => $refreshExpiredAt->toDateTimeString(),
        ];
    }

    private function invalidateUserToken($user): void
    {
        $jwtKey = 'jwt_' . $user->id;
        $oldJwt = Cache::get($jwtKey);
        if ($oldJwt && JWTAuth::setToken($oldJwt)->check()) {
            JWTAuth::setToken($oldJwt)->invalidate();
            Cache::forget($jwtKey);
        }
    }
}
