<?php

declare(strict_types=1);

namespace App\Contracts;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(array $credentials): array;

    public function logout(): array;

    public function refresh(): array;

    public function me(): array;

    public function forgotPassword(string $email): array;

    public function resetPassword(string $token, string $password): array;

    public function changePassword(string $currentPassword, string $newPassword): array;

    public function verifyEmail(string $id, string $hash): array;

    public function resendVerificationEmail(string $email): array;
}
