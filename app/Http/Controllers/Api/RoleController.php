<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    public function index()
    {
        try {
            $roles = $this->rolePermissionService->getAllRoles();
            return RoleResource::collection($roles);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rollar əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateRoleRequest $request)
    {
        try {
            $role = $this->rolePermissionService->createRole($request->validated());

            if ($request->has('permissions')) {
                $this->rolePermissionService->assignPermissionsToRole(
                    $role->id,
                    $request->permissions
                );
            }

            return response()->json([
                'message' => 'Rol uğurla yaradıldı',
                'role' => new RoleResource($role->load('permissions'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol yaradılarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $role = $this->rolePermissionService->getRoleWithPermissions($id);
            return new RoleResource($role);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol tapılmadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(CreateRoleRequest $request, $id)
    {
        try {
            $role = $this->rolePermissionService->updateRole($id, $request->validated());

            if ($request->has('permissions')) {
                $this->rolePermissionService->assignPermissionsToRole(
                    $id,
                    $request->permissions
                );
            }

            return response()->json([
                'message' => 'Rol uğurla yeniləndi',
                'role' => new RoleResource($role->load('permissions'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->rolePermissionService->deleteRole($id);

            return response()->json([
                'message' => 'Rol uğurla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignPermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = $this->rolePermissionService->assignPermissionsToRole(
                $id,
                $request->permissions
            );

            return response()->json([
                'message' => 'İcazələr uğurla təyin edildi',
                'role' => new RoleResource($role->load('permissions'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazələr təyin edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removePermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = $this->rolePermissionService->removePermissionsFromRole(
                $id,
                $request->permissions
            );

            return response()->json([
                'message' => 'İcazələr uğurla silindi',
                'role' => new RoleResource($role->load('permissions'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazələr silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignToUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $role = Role::findOrFail($id);
            $user = $this->rolePermissionService->assignRoleToUser(
                $request->user_id,
                $role->name
            );

            return response()->json([
                'message' => 'Rol uğurla istifadəçiyə təyin edildi',
                'user' => new UserResource($user->load('roles'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol təyin edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoleUsers($id)
    {
        try {
            $users = $this->rolePermissionService->getRoleUsers($id);
            return UserResource::collection($users);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol istifadəçiləri əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
