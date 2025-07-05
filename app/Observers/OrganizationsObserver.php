<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Organizations;
use Illuminate\Support\Facades\Cache;

class OrganizationsObserver
{
    public function created(Organizations $organization): void
    {
        $this->clearCache();
    }

    public function updated(Organizations $organization): void
    {
        $this->clearCache();
    }

    public function deleted(Organizations $organization): void
    {
        $this->clearCache();
    }

    public function restored(Organizations $organization): void
    {
        $this->clearCache();
    }

    /**
     * 清除組織樹 cache
     */
    private function clearCache(): void
    {
        Cache::forget('organizations_tree');
    }
}
