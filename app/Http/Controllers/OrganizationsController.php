<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\OrganizationsServiceInterface;
use App\Http\Requests\Organizations\StoreOrganizationsRequest;
use App\Http\Requests\Organizations\UpdateOrganizationsRequest;
use App\Http\Requests\Organizations\IndexOrganizationsRequest;
use App\Http\Requests\Organizations\ShowOrganizationsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class OrganizationsController extends Controller
{
    protected $organizationsService;

    public function __construct(OrganizationsServiceInterface $organizationsService)
    {
        $this->organizationsService = $organizationsService;
    }

    /**
     * Display all Organizations (supports pagination, sorting, relationships, filtering)
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     data: array{
     *       id: string,
     *       name: string,
     *       type: string,
     *       parent_id: string|null,
     *       manager_user_id: string|null,
     *       address: string|null,
     *       phone: string|null,
     *       email: string|null,
     *       monthly_budget: string|null,
     *       approval_settings: array|null,
     *       settings: array|null,
     *       cost_center_code: string|null,
     *       status: string,
     *       created_at: string,
     *       updated_at: string,
     *       parent: array|null,
     *       children: array|null,
     *       manager: array|null,
     *       users: array|null
     *     }[],
     *     meta: array{
     *       current_page: int,
     *       per_page: int,
     *       total: int,
     *       last_page: int,
     *       from: int|null,
     *       to: int|null
     *     }
     *   },
     *   timestamp: int
     * }
     */
    public function index(IndexOrganizationsRequest $request): JsonResponse
    {
        try {
            $result = $this->organizationsService->index(
                $request->input('per_page') ?? 0,
                $request->input('order_by', 'created_at'),
                $request->input('order_direction', 'asc'),
                $request->input('with', []),
                $request->get('columns', ['*']),
                $request->input('filters', [])
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->handleException($e, __('get-organizations-failed'), 500);
        }
    }

    /**
     * Display specific Organization
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     parent_id: string|null,
     *     manager_user_id: string|null,
     *     address: string|null,
     *     phone: string|null,
     *     email: string|null,
     *     monthly_budget: string|null,
     *     approval_settings: array|null,
     *     settings: array|null,
     *     cost_center_code: string|null,
     *     status: string,
     *     created_at: string,
     *     updated_at: string,
     *     parent: array|null,
     *     children: array|null,
     *     manager: array|null,
     *     users: array|null
     *   },
     *   timestamp: int
     * }
     */
    public function show(ShowOrganizationsRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->organizationsService->find($id, $request->get('columns', ['*']), $request->input('with', []));

            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-organizations-info-failed'), 500);
        }
    }

    /**
     * Create new Organization
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 201,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     parent_id: string|null,
     *     manager_user_id: string|null,
     *     address: string|null,
     *     phone: string|null,
     *     email: string|null,
     *     monthly_budget: string|null,
     *     approval_settings: array|null,
     *     settings: array|null,
     *     cost_center_code: string|null,
     *     status: string,
     *     created_at: string,
     *     updated_at: string
     *   },
     *   timestamp: int
     * }
     * @status 201
     */
    public function store(StoreOrganizationsRequest $request): JsonResponse
    {
        try {
            $result = $this->organizationsService->create($request->validated());

            return response()->json($result, 201);
        } catch (Exception $e) {
            return $this->handleException($e, __('create-organizations-failed'), 500);
        }
    }

    /**
     * Update Organization
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     parent_id: string|null,
     *     manager_user_id: string|null,
     *     address: string|null,
     *     phone: string|null,
     *     email: string|null,
     *     monthly_budget: string|null,
     *     approval_settings: array|null,
     *     settings: array|null,
     *     cost_center_code: string|null,
     *     status: string,
     *     created_at: string,
     *     updated_at: string
     *   },
     *   timestamp: int
     * }
     */
    public function update(UpdateOrganizationsRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->organizationsService->update($id, $request->validated());

            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('update-organizations-failed'), 500);
        }
    }

    /**
     * Delete Organization
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: null,
     *   timestamp: int
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->organizationsService->delete($id);
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('delete-organizations-failed'), 500);
        }
    }

    /**
     * 取得完整組織樹狀結構（遞迴載入全部子組織）
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     parent_id: string|null,
     *     status: string,
     *     children: array{
     *       id: string,
     *       name: string,
     *       type: string,
     *       parent_id: string,
     *       status: string,
     *       children: array{
     *         id: string,
     *         name: string,
     *         type: string,
     *         parent_id: string,
     *         status: string,
     *         children: array
     *       }[]
     *     }[]
     *   }[],
     *   timestamp: int
     * }
     */
    public function tree(): JsonResponse
    {
        try {
            $tree = $this->organizationsService->getOrganizationTree();
            return response()->json($tree);
        } catch (\Throwable $e) {
            return $this->handleException($e, __('organization_tree_failed'), 500);
        }
    }

    /**
     * 取得指定組織的子組織列表（遞迴載入全部子組織）
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     parent_id: string,
     *     status: string,
     *     children: array{
     *       id: string,
     *       name: string,
     *       type: string,
     *       parent_id: string,
     *       status: string,
     *       children: array{
     *         id: string,
     *         name: string,
     *         type: string,
     *         parent_id: string,
     *         status: string,
     *         children: array
     *       }[]
     *     }[]
     *   }[],
     *   timestamp: int
     * }
     */
    public function children(string $id): JsonResponse
    {
        try {
            $children = $this->organizationsService->getChildren($id);
            return response()->json($children);
        } catch (\Throwable $e) {
            return $this->handleException($e, __('organization_children_failed'), 500);
        }
    }

    /**
     * 取得組織成員列表，含角色權限
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     email: string,
     *     phone: string|null,
     *     employee_id: string|null,
     *     is_active: boolean,
     *     email_verified_at: string|null,
     *     created_at: string,
     *     updated_at: string,
     *     pivot: array{
     *       organization_id: string,
     *       user_id: string,
     *       created_at: string,
     *       updated_at: string
     *     },
     *     roles: array{
     *       id: string,
     *       name: string,
     *       guard_name: string,
     *       created_at: string,
     *       updated_at: string,
     *       permissions: array{
     *         id: string,
     *         name: string,
     *         guard_name: string,
     *         created_at: string,
     *         updated_at: string
     *       }[]
     *     }[]
     *   }[],
     *   timestamp: int
     * }
     */
    public function users(string $id): JsonResponse
    {
        try {
            $users = $this->organizationsService->getUsersWithRoles($id);
            return response()->json($users);
        } catch (\Throwable $e) {
            return $this->handleException($e, __('organization_users_failed'), 500);
        }
    }

    /**
     * 取得組織統計數據（報銷、預算、人員）
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     organization_id: string,
     *     organization_name: string,
     *     total_users: int,
     *     active_users: int,
     *     inactive_users: int,
     *     monthly_budget: string,
     *     budget_used: string,
     *     budget_remaining: string,
     *     budget_usage_percentage: float,
     *     total_expenses: int,
     *     pending_expenses: int,
     *     approved_expenses: int,
     *     rejected_expenses: int,
     *     total_expense_amount: string,
     *     average_expense_amount: string,
     *     last_expense_date: string|null,
     *     created_at: string,
     *     updated_at: string
     *   },
     *   timestamp: int
     * }
     */
    public function stats(string $id): JsonResponse
    {
        try {
            $stats = $this->organizationsService->getStats($id);
            return response()->json($stats);
        } catch (\Throwable $e) {
            return $this->handleException($e, __('organization_stats_failed'), 500);
        }
    }

    protected function handleException(\Exception $e, string $defaultMessage, int $defaultCode = 500): \Illuminate\Http\JsonResponse
    {
        return parent::handleException($e, $defaultMessage, $defaultCode);
    }
}
