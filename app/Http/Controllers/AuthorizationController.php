<?php

namespace App\Http\Controllers;

use App\Contracts\AuthorizationServiceInterface;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\AssignPermissionToRoleRequest;
use App\Http\Requests\AssignRolesToUserRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthorizationController extends Controller
{
    protected $authorizationService;

    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Get all roles
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     guard_name: string,
     *     created_at: string,
     *     updated_at: string,
     *     permissions: array|null
     *   }[],
     *   timestamp: int,
     *   success: true
     * }
     */
    public function index(): JsonResponse
    {
        try {
            $roles = $this->authorizationService->getRoles();
            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => __('get-roles-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-roles-failed'), 500);
        }
    }

    /**
     * Create new role
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 201,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     guard_name: string,
     *     created_at: string,
     *     updated_at: string
     *   },
     *   timestamp: int,
     *   success: true
     * }
     * @status 201
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $result = $this->authorizationService->createRole($request->validated());
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => __('create-role-success')
            ], 201);
        } catch (Exception $e) {
            return $this->handleException($e, __('create-role-failed'), 500);
        }
    }

    /**
     * Get role detail
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     guard_name: string,
     *     created_at: string,
     *     updated_at: string,
     *     permissions: array|null
     *   },
     *   timestamp: int,
     *   success: true
     * }
     */
    public function show($id): JsonResponse
    {
        try {
            $role = $this->authorizationService->getRoleDetail($id);
            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => __('get-role-detail-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-role-detail-failed'), 404);
        }
    }

    /**
     * Update role
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     guard_name: string,
     *     created_at: string,
     *     updated_at: string
     *   },
     *   timestamp: int,
     *   success: true
     * }
     */
    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->authorizationService->updateRole($id, $request->validated());
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => __('update-role-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('update-role-failed'), 500);
        }
    }

    /**
     * Delete role
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: null,
     *   timestamp: int,
     *   success: true
     * }
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->authorizationService->deleteRole($id);
            return response()->json([
                'success' => true,
                'message' => __('delete-role-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('delete-role-failed'), 500);
        }
    }

    /**
     * Assign permissions to role
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: null,
     *   timestamp: int,
     *   success: true
     * }
     */
    public function assignPermissionToRole(AssignPermissionToRoleRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->authorizationService->assignPermissionToRole($id, $request->permissions);
            return response()->json([
                'success' => true,
                'message' => __('assign-permission-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('assign-permission-failed'), 500);
        }
    }

    /**
     * Get all permissions
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: array{
     *     id: string,
     *     name: string,
     *     guard_name: string,
     *     created_at: string,
     *     updated_at: string
     *   }[],
     *   timestamp: int,
     *   success: true
     * }
     */
    public function permissionIndex(): JsonResponse
    {
        try {
            $result = $this->authorizationService->getPermissions();
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => __('get-permissions-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-permissions-failed'), 500);
        }
    }

    /**
     * Assign roles to user
     * 
     * @response array{
     *   status: "success",
     *   statusCode: 200,
     *   message: string,
     *   result: null,
     *   timestamp: int,
     *   success: true
     * }
     */
    public function assignRolesToUser(AssignRolesToUserRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->authorizationService->assignRolesToUser($id, $request->roles);
            return response()->json([
                'success' => true,
                'message' => __('assign-roles-success')
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, __('assign-roles-failed'), 500);
        }
    }
}
