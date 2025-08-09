<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    public function index()
    {
        try {
            $permissions = $this->rolePermissionService->getAllPermissions();
            return PermissionResource::collection($permissions);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazələr əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'module' => 'required|string'
        ]);

        try {
            $data = $request->only("name", "display_name", "description", "module");
            $permission = $this->rolePermissionService->createPermission($data);

            return response()->json([
                'message' => 'İcazə uğurla yaradıldı',
                'permission' => new PermissionResource($permission)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazə yaradılarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return new PermissionResource($permission);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazə tapılmadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'module' => 'required|string'
        ]);

        $data = $request->only("name", "display_name", "description", "module");

        try {
            $permission = Permission::findOrFail($id);
            $permission->update($data);

            return response()->json([
                'message' => 'İcazə uğurla yeniləndi',
                'permission' => new PermissionResource($permission)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazə yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'message' => 'İcazə uğurla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazə silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByModule($module)
    {
        try {
            $permissions = $this->rolePermissionService->getPermissionsByModule($module);
            return PermissionResource::collection($permissions);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Modul icazələri əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
