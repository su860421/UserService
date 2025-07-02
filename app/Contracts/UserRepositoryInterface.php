<?php

declare(strict_types=1);

namespace App\Contracts;

use JoeSu\LaravelScaffold\BaseRepositoryInterface;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * 依 email 查詢單一使用者
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    // Add custom methods here
}
