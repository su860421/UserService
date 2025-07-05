<?php

declare(strict_types=1);

namespace App\Contracts;

use JoeSu\LaravelScaffold\BaseServiceInterface;

interface OrganizationsServiceInterface extends BaseServiceInterface
{
    /**
     * 取得完整組織樹狀結構
     */
    public function getOrganizationTree();

    /**
     * 取得指定組織的子組織列表
     */
    public function getChildren(string $id);

    /**
     * 取得組織成員列表，含角色權限
     */
    public function getUsersWithRoles(string $id);

    /**
     * 取得組織統計數據（報銷、預算、人員）
     */
    public function getStats(string $id);

    // Add custom methods here
}
