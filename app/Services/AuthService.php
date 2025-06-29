<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Contracts\UserRepositoryInterface;
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

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function register(array $data): array
    {
        try {
            // 開始資料庫交易
            DB::beginTransaction();

            // 1. 建立使用者
            $user = $this->userRepository->create($data);

            // 2. 發送驗證郵件
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $emailException) {
                Log::error('驗證郵件發送失敗', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $emailException->getMessage()
                ]);

                // 如果發送郵件失敗，回滾交易
                DB::rollBack();

                return [
                    'success' => false,
                    'message' => '註冊成功，但驗證郵件發送失敗',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'employee_id' => $user->employee_id,
                        ],
                        'email_verified' => false,
                        'warning' => '請稍後重新發送驗證郵件',
                        'error' => $emailException->getMessage()
                    ]
                ];
            }

            // 3. 提交交易
            DB::commit();

            return [
                'success' => true,
                'message' => __('register-success'),
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'employee_id' => $user->employee_id,
                    ],
                    'email_verified' => false,
                    'message' => '請檢查您的電子郵件並點擊驗證連結以完成註冊。'
                ]
            ];
        } catch (\Exception $e) {
            // 發生任何錯誤時回滾交易
            DB::rollBack();

            Log::error('註冊失敗', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => '註冊失敗，請稍後再試',
                'error' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => __('invalid-credentials')
            ];
        }

        $user = Auth::user();

        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => __('account-disabled')
            ];
        }

        if (!$user->email_verified_at) {
            return [
                'success' => false,
                'message' => __('email-not-verified'),
                'data' => [
                    'email_verified' => false,
                    'message' => '請先驗證您的電子郵件地址才能登入。'
                ]
            ];
        }

        $response = $this->generateTokenResponse($user);

        return [
            'success' => true,
            'message' => __('login-success'),
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'employee_id' => $user->employee_id,
                ],
                'token' => $response['access_token'],
                'token_type' => $response['token_type'],
                'expires_in' => $response['expires_in'],
                'expired_at' => $response['expired_at'],
                'email_verified' => true,
            ]
        ];
    }

    public function logout(): array
    {
        try {
            $user = Auth::user();
            if ($user) {
                $this->invalidateUserToken($user);
            }

            return [
                'success' => true,
                'message' => __('logout-success')
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => __('logout-failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function refresh(): array
    {
        try {
            $token = JWTAuth::refresh();
            $user = Auth::user();
            $response = $this->generateTokenResponse($user);

            return [
                'success' => true,
                'message' => __('token-refresh-success'),
                'data' => [
                    'token' => $response['access_token'],
                    'token_type' => $response['token_type'],
                    'expires_in' => $response['expires_in'],
                    'expired_at' => $response['expired_at'],
                ]
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => __('token-refresh-failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function me(): array
    {
        try {
            $user = Auth::user();
            return [
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'employee_id' => $user->employee_id,
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at,
                    ]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => __('get-user-info-failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function forgotPassword(string $email): array
    {
        try {
            DB::beginTransaction();

            $users = $this->userRepository->index(1, null, 'asc', [], ['*'], [['email', '=', $email]]);

            if ($users->isEmpty()) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => __('user-not-found')
                ];
            }

            $user = $users->first();

            // 檢查使用者是否已驗證電子郵件
            if (!$user->email_verified_at) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => '請先驗證您的電子郵件地址才能重設密碼'
                ];
            }

            $status = Password::sendResetLink(['email' => $email]);

            if ($status === Password::RESET_LINK_SENT) {
                DB::commit();
                return [
                    'success' => true,
                    'message' => __('forgot-password-success')
                ];
            } else {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => __('forgot-password-failed')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Forgot password failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => '密碼重設請求失敗，請稍後再試',
                'error' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    public function resetPassword(string $token, string $password): array
    {
        try {
            DB::beginTransaction();

            $status = Password::reset(
                ['token' => $token, 'password' => $password],
                function ($user, $password) {
                    $this->userRepository->update($user->id, [
                        'password' => Hash::make($password)
                    ]);

                    $this->invalidateUserToken($user);
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                DB::commit();
                return [
                    'success' => true,
                    'message' => __('reset-password-success')
                ];
            } else {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => __('reset-password-failed')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Reset password failed', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => '密碼重設失敗，請稍後再試',
                'error' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    public function changePassword(string $currentPassword, string $newPassword): array
    {
        $user = Auth::user();

        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => __('current-password-wrong')
            ];
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword)
        ]);

        $this->invalidateUserToken($user);

        return [
            'success' => true,
            'message' => __('change-password-success')
        ];
    }

    public function verifyEmail(string $id, string $hash): array
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return [
                'success' => false,
                'message' => __('user-not-found')
            ];
        }

        if ($user->email_verified_at) {
            return [
                'success' => true,
                'message' => __('email-already-verified')
            ];
        }

        if (!hash_equals(sha1($user->email), $hash)) {
            return [
                'success' => false,
                'message' => __('invalid-verification-link')
            ];
        }

        $this->userRepository->update($user->id, [
            'email_verified_at' => now()
        ]);

        return [
            'success' => true,
            'message' => __('email-verified-success')
        ];
    }

    public function resendVerificationEmail(string $email): array
    {
        $users = $this->userRepository->index(1, null, 'asc', [], ['*'], [['email', '=', $email]]);

        if ($users->isEmpty()) {
            return [
                'success' => false,
                'message' => __('user-not-found')
            ];
        }

        $user = $users->first();

        if ($user->email_verified_at) {
            return [
                'success' => false,
                'message' => __('email-already-verified')
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            'success' => true,
            'message' => __('verification-email-sent')
        ];
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
        Cache::put($jwtKey, $jwtToken, $expiredAt);

        return [
            'access_token' => $jwtToken,
            'token_type' => 'bearer',
            'expires_in' => $expiresInSeconds,
            'expired_at' => $expiredAt,
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
