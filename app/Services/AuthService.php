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
use Illuminate\Support\Facades\URL;

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
            throw new ServiceException(__('invalid-credentials'), 401, null);
        }
        $user = Auth::user();
        if (!$user->is_active) {
            throw new ServiceException(__('account-disabled'), 403, null);
        }
        if (! $user->hasVerifiedEmail()) {
            throw new ServiceException(__('email-not-verified'), 403, null);
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
            throw new ServiceException(__('refresh-token-invalid'), 401, null);
        }
        $user = $this->userRepository->find($data['user_id']);
        if (!$user) {
            Cache::forget($key);
            throw new ServiceException(__('user-not-found'), 404, null);
        }
        Cache::forget($key);
        return $this->generateTokenResponse($user);
    }

    public function me(): User
    {
        $user = Auth::user();
        if (!$user) {
            throw new ServiceException(__('user-not-found'), 404, null);
        }
        return $user;
    }

    public function forgotPassword(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new ServiceException(__('user-not-found'), 404, null);
        }

        // 產生 Laravel 預設的密碼重設 token
        $token = Password::createToken($user);

        // 產生前端的重設密碼頁面連結
        $frontEndUrl = config('app.frontend_url', 'http://localhost:3000'); // 請於 .env 設定 FRONTEND_URL
        $frontEndLink = $frontEndUrl . '/reset-password?token=' . urlencode($token) . '&email=' . urlencode($user->email);

        $user->sendEmailResetPasswordNotification($frontEndLink);
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
                throw new ServiceException(__('reset-password-failed'), 500, null);
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
            throw new ServiceException(__('current-password-wrong'), 400, null);
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
            throw new ServiceException(__('user-not-found'), 404, null);
        }
        if ($user->email_verified_at) {
            return;
        }
        if (!hash_equals(sha1($user->email), $hash)) {
            throw new ServiceException(__('invalid-verification-link'), 400, null);
        }
        $this->userRepository->update($user->id, [
            'email_verified_at' => now()
        ]);
    }

    public function resendVerificationEmail(string $email): void
    {
        $users = $this->userRepository->index(1, null, 'asc', [], ['*'], [['email', '=', $email]]);
        if ($users->isEmpty()) {
            throw new ServiceException(__('user-not-found'), 404, null);
        }
        $user = $users->first();
        if ($user->email_verified_at) {
            throw new ServiceException(__('email-already-verified'), 400, null);
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
