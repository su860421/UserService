<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrganizationsServiceInterface;
use App\Contracts\OrganizationsRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrganizationsService extends BaseService implements OrganizationsServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        OrganizationsRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct($repository);
        $this->userRepository = $userRepository;
    }

    /**
     * 取得完整組織樹狀結構
     */
    public function getOrganizationTree()
    {
        Log::info(Cache::get('organizations_tree'));
        return Cache::remember(
            'organizations_tree',
            Carbon::now()->diffInSeconds(Carbon::tomorrow()), // 正向計算
            function () {
                Log::info('getOrganizationTree');
                return $this->repository->index(
                    perPage: 0,
                    orderBy: 'name',
                    orderDirection: 'asc',
                    relationships: ['childrenRecursive'],
                    columns: ['id', 'name', 'type', 'parent_id', 'status'],
                    filters: [['parent_id', '=', null]]
                );
            }
        );
    }

    /**
     * 取得指定組織的子組織列表
     */
    public function getChildren(string $id)
    {
        $fullTree = $this->getOrganizationTree();
        return $this->findChildrenFromTree($fullTree, $id);
    }

    /**
     * 從完整組織樹中遞迴查找指定組織的子組織
     */
    private function findChildrenFromTree($organizations, string $parentId)
    {
        foreach ($organizations as $org) {
            if ($org->id === $parentId) {
                return $org->childrenRecursive ?? collect();
            }

            if (isset($org->childrenRecursive) && $org->childrenRecursive->isNotEmpty()) {
                $result = $this->findChildrenFromTree($org->childrenRecursive, $parentId);
                if ($result->isNotEmpty()) {
                    return $result;
                }
            }
        }

        return collect();
    }

    /**
     * 取得組織成員列表（分頁，僅撈必要欄位）
     * @param string $id
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersWithRoles(string $id, int $perPage = 20): LengthAwarePaginator
    {
        // 直接使用 UserRepository 進行跨關聯分頁查詢
        // 組織存在性驗證在 UserRepository 中處理
        return $this->userRepository->getUsersByOrganization($id, $perPage);
    }

    /**
     * 取得組織統計數據（人員、預算）
     */
    public function getStats(string $id)
    {
        $org = $this->repository->find($id, relationships: ['users.count']);

        // TODO:報銷未來須修正
        return [
            'reimbursements' => 0, // 報銷暫無資料，預設為 0
            'budget' => $org->monthly_budget ?? 0,
            'members' => $org->users_count ?? 0,
        ];
    }

    // Add business logic methods here
}
