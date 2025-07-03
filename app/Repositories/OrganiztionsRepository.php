<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrganiztionsRepositoryInterface;
use App\Models\Organiztions;
use JoeSu\LaravelScaffold\BaseRepository;

class OrganiztionsRepository extends BaseRepository implements OrganiztionsRepositoryInterface
{
    public function __construct(Organiztions $model)
    {
        parent::__construct($model);
    }

    // Add custom methods here
}
