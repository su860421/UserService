<?php

namespace App\Http\Controllers;

use App\Contracts\AuthorizationServiceInterface;
use App\Http\Requests\AssignPermissionToRoleRequest;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    protected $authorizationService;

    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    public function getRole()
    {
        try {
            $roles = $this->authorizationService->getRoles();
            return response()->json(['success' => true, 'data' => $roles]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('取得角色列表失敗'), 500);
        }
    }

    public function createRole(\App\Http\Requests\StoreRoleRequest $request)
    {
        try {
            $role = $this->authorizationService->createRole($request->validated());
            return response()->json(['success' => true, 'data' => $role, 'message' => __('建立角色成功')]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('建立角色失敗'), 500);
        }
    }

    public function getRoleDetail($id)
    {
        try {
            $role = $this->authorizationService->getRoleDetail($id);
            return response()->json(['success' => true, 'data' => $role]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('取得角色資訊失敗'), 404);
        }
    }

    public function updateRole(\App\Http\Requests\UpdateRoleRequest $request, $id)
    {
        try {
            $role = $this->authorizationService->updateRole($id, $request->validated());
            return response()->json(['success' => true, 'data' => $role, 'message' => __('更新角色成功')]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('更新角色失敗'), 500);
        }
    }

    public function deleteRole($id)
    {
        try {
            $this->authorizationService->deleteRole($id);
            return response()->json(['success' => true, 'message' => __('刪除角色成功')]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('刪除角色失敗'), 500);
        }
    }

    public function assignPermissionToRole(\App\Http\Requests\AssignPermissionToRoleRequest $request, $id)
    {
        try {
            $role = $this->authorizationService->assignPermissionToRole($id, $request->permissions);
            return response()->json(['success' => true, 'data' => $role, 'message' => __('權限分配成功')]);
        } catch (\Exception $e) {
            return $this->handleException($e, __('權限分配失敗'), 500);
        }
    }
}
