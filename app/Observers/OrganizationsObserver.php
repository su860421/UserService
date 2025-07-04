<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Organizations;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OrganizationsObserver
{
    public function created(Organizations $organization): void
    {
        $this->clearCache($organization);
    }

    public function updated(Organizations $organization): void
    {
        $this->clearCache($organization);
    }

    public function deleted(Organizations $organization): void
    {
        $this->clearCache($organization);
    }

    public function restored(Organizations $organization): void
    {
        $this->clearCache($organization);
    }

    private function clearCache(Organizations $organization): void
    {
        $today = Carbon::today()->format('Y-m-d');

        // 清除整棵組織樹 cache（當日）
        Cache::forget("organizations_tree_{$today}");
        // 清除該組織的 children cache（當日）
        Cache::forget("organizations_children_{$organization->id}_{$today}");
        // 清除父組織的 children cache（當日，如果有父組織）
        if ($organization->parent_id) {
            Cache::forget("organizations_children_{$organization->parent_id}_{$today}");
        }
    }
}
