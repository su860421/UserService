<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrganizationsServiceInterface;
use App\Contracts\OrganizationsRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * 取得完整組織樹狀結構（僅 root + 第一層，避免全撈造成效能問題）
     * 如需完整樹狀，建議前端 lazy load 或快取
     */
    public function getOrganizationTree()
    {
        return $this->repository->index(
            perPage: 0, // 不分頁，取得全部
            relationships: ['children'], // 預載入子組織
            filters: [['parent_id', '=', null]] // 只取 root 組織
        );
    }

    /**
     * 取得指定組織的子組織列表（單層）
     */
    public function getChildren(string $id)
    {
        return $this->repository->index(
            perPage: 0, // 不分頁，取得全部
            filters: [['parent_id', '=', $id]] // 指定父組織
        );
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
