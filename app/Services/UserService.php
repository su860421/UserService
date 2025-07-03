<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
    }

    // Add business logic methods here

    /**
     * 批次同步 user 與 organizations 的關聯
     *
     * @param \App\Models\User $user
     * @param array $organizationIds
     * @return void
     */
    public function syncOrganizations(\App\Models\User $user, array $organizationIds): void
    {
        $user->organizations()->sync($organizationIds);
    }
}
