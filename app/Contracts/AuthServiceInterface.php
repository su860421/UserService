<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): User;

    public function login(array $credentials): array;

    public function logout(): void;

    public function refresh(string $refreshToken): array;

    public function me(): User;

    public function forgotPassword(string $email): void;

    public function resetPassword(string $token, string $password): void;

    public function changePassword(string $currentPassword, string $newPassword): void;

    public function verifyEmail(string $id, string $hash): void;

    public function resendVerificationEmail(string $email): void;
}
