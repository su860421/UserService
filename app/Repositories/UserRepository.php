<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use JoeSu\LaravelScaffold\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use JoeSu\LaravelScaffold\Exceptions\RepositoryException;
use Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * 依 email 查詢單一使用者
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * 取得指定組織的成員列表（分頁）
     *
     * @param string $organizationId
     * @param int $perPage
     * @return LengthAwarePaginator
     * @throws RepositoryException
     */
    public function getUsersByOrganization(string $organizationId, int $perPage = 20): LengthAwarePaginator
    {
        try {
            $query = $this->model
                ->join('organization_user', 'users.id', '=', 'organization_user.user_id')
                ->where('organization_user.organization_id', $organizationId)
                ->select(['users.id', 'users.name', 'users.email']);

            // 如果查詢結果為空，可能表示組織不存在
            $result = $query->paginate($perPage);

            // 如果沒有資料且是第一頁，檢查組織是否存在
            if ($result->count() === 0 && $result->currentPage() === 1) {
                $organizationExists = \App\Models\Organizations::where('id', $organizationId)->exists();
                if (!$organizationExists) {
                    Log::warning('Organization not found when querying users', [
                        'organization_id' => $organizationId,
                        'method' => __METHOD__
                    ]);
                    throw new RepositoryException('Organization not found', 404);
                }
            }

            return $result;
        } catch (RepositoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to get organization users', [
                'organization_id' => $organizationId,
                'per_page' => $perPage,
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            throw new RepositoryException(
                'Failed to get organization users: ' . $e->getMessage(),
                500
            );
        }
    }
}
