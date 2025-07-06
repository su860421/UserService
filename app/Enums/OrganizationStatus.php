<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

enum OrganizationStatus: int
{
    use Values;

    case INACTIVE = 0;
    case ACTIVE = 1;
}
