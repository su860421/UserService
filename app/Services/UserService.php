<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    // Add business logic methods here
}
