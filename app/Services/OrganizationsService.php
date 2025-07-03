<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrganizationsServiceInterface;
use App\Contracts\OrganizationsRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;

class OrganizationsService extends BaseService implements OrganizationsServiceInterface
{
    public function __construct(OrganizationsRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    // Add business logic methods here
}
