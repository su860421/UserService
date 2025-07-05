<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrganizationsRepositoryInterface;
use App\Models\Organizations;
use JoeSu\LaravelScaffold\BaseRepository;

class OrganizationsRepository extends BaseRepository implements OrganizationsRepositoryInterface
{
    public function __construct(Organizations $model)
    {
        parent::__construct($model);
    }

    // Add custom methods here
}
