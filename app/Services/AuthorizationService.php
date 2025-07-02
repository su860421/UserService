<?php

namespace App\Services;

use App\Contracts\AuthorizationServiceInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AuthorizationService implements AuthorizationServiceInterface
{
    public function getRoles(array $filters = [])
    {
        return Role::with('permissions')->get();
    }

    public function createRole(array $data)
    {
        return Role::create($data);
    }

    public function getRoleDetail(int $id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function updateRole(int $id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function deleteRole(int $id)
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }

    public function assignPermissionToRole(int $roleId, array $permissionIds)
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($permissionIds);
        return $role->load('permissions');
    }
}
