<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrganiztionsServiceInterface;
use App\Contracts\OrganiztionsRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;

class OrganiztionsService extends BaseService implements OrganiztionsServiceInterface
{
    public function __construct(OrganiztionsRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    // Add business logic methods here
}
