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

class AuthorizationController extends Controller
{
    protected $authorizationService;

    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    public function index()
    {
        try {
            $roles = $this->authorizationService->getRoles();
            return response()->json(['success' => true, 'data' => $roles]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('get-roles-failed'), 500);
        }
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $result = $this->authorizationService->createRole($request->validated());
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e, __('create-role-failed'), 500);
        }
    }

    public function show($id)
    {
        try {
            $role = $this->authorizationService->getRoleDetail($id);
            return response()->json(['success' => true, 'data' => $role]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('get-role-detail-failed'), 404);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $result = $this->authorizationService->updateRole($id, $request->validated());
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e, __('update-role-failed'), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->authorizationService->deleteRole($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e, __('delete-role-failed'), 500);
        }
    }

    public function assignPermissionToRole(AssignPermissionToRoleRequest $request, $id)
    {
        try {
            $result = $this->authorizationService->assignPermissionToRole($id, $request->permissions);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e, __('assign-permission-failed'), 500);
        }
    }

    public function permissionIndex()
    {
        try {
            $result = $this->authorizationService->getPermissions();
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e, __('get-permissions-failed'), 500);
        }
    }

    public function assignRolesToUser(AssignRolesToUserRequest $request, $id)
    {
        try {
            $result = $this->authorizationService->assignRolesToUser($id, $request->roles);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e, __('assign-roles-failed'), 500);
        }
    }
}
