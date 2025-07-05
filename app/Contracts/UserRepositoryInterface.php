<?php

declare(strict_types=1);

namespace App\Contracts;

use JoeSu\LaravelScaffold\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * 依 email 查詢單一使用者
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * 取得指定組織的成員列表（分頁）
     *
     * @param string $organizationId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersByOrganization(string $organizationId, int $perPage = 20): LengthAwarePaginator;

    // Add custom methods here
}
