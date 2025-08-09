<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Resources\UserResource;
use App\Services\RolePermissionService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $rolePermissionService;
    protected $userRepository;

    public function __construct(
        RolePermissionService $rolePermissionService,
        UserRepositoryInterface $userRepository
    ) {
        $this->rolePermissionService = $rolePermissionService;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $users = $this->userRepository->paginate($perPage);

            return UserResource::collection($users);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İstifadəçilər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userRepository->getUserWithRoles($id);
            return new UserResource($user);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İstifadəçi tapılmadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function assignRole(AssignRoleRequest $request, $id)
    {
        try {
            $user = $this->rolePermissionService->assignRoleToUser($id, $request->role);

            return response()->json([
                'message' => 'Rol uğurla təyin edildi',
                'user' => new UserResource($user->load('roles'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol təyin edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeRole(AssignRoleRequest $request, $id)
    {
        try {
            $user = $this->rolePermissionService->removeRoleFromUser($id, $request->role);

            return response()->json([
                'message' => 'Rol uğurla silindi',
                'user' => new UserResource($user->load('roles'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rol silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(UpdateUserStatusRequest $request, $id)
    {
        try {
            $user = $this->userRepository->update($id, ['status' => $request->status]);

            return response()->json([
                'message' => 'İstifadəçi statusu uğurla yeniləndi',
                'user' => new UserResource($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Status yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserPermissions($id)
    {
        try {
            $permissions = $this->rolePermissionService->getUserPermissions($id);

            return response()->json([
                'user_id' => $id,
                'permissions' => $permissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'İcazələr əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserStatistics($id)
    {
        try {
            $statistics = $this->userRepository->getUserStatistics($id);

            return response()->json([
                'user_id' => $id,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Statistika əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search($search)
    {
        try {
            $users = $this->userRepository->searchUsers($search);
            return UserResource::collection($users);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Axtarış zamanı səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTeachers()
    {
        try {
            $teachers = $this->userRepository->getTeachers();
            return UserResource::collection($teachers);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Müəllimlər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudents()
    {
        try {
            $students = $this->userRepository->getStudents();
            return UserResource::collection($students);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Şagirdlər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveUsers()
    {
        try {
            $users = $this->userRepository->getActiveUsers();
            return UserResource::collection($users);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Aktiv istifadəçilər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
