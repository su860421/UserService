<?php

namespace App\Contracts;

interface AuthorizationServiceInterface
{
    public function getRoles(array $filters = []);
    public function createRole(array $data);
    public function getRoleDetail(int $id);
    public function updateRole(int $id, array $data);
    public function deleteRole(int $id);
    public function assignPermissionToRole(int $roleId, array $permissionIds);
    public function getPermissions();
    public function assignRolesToUser(int $userId, array $roles);
}
