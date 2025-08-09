<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\RolePermissionService;
use App\Enums\UserStatus;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    protected $userRepository;
    protected $rolePermissionService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RolePermissionService $rolePermissionService
    ) {
        $this->userRepository = $userRepository;
        $this->rolePermissionService = $rolePermissionService;
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:teacher,student'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Doğrulama səhvləri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->userRepository->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => UserStatus::ACTIVE
            ]);

            $user->assignRole($request->role);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'İstifadəçi uğurla qeydiyyatdan keçdi',
                'user' => new UserResource($user->load('roles')),
                'token' => $token,
                'token_type' => 'Bearer'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Qeydiyyat zamanı səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Doğrulama səhvləri',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Daxil edilən məlumatlar yanlışdır.'],
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Sizin hesabınız deaktiv edilmişdir.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        $permissions = $this->rolePermissionService->getUserPermissions($user->id);

        return response()->json([
            'message' => 'Uğurla daxil oldunuz',
            'user' => new UserResource($user->load('roles')),
            'permissions' => $permissions,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }


    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Uğurla çıxış etdiniz'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Çıxış zamanı səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function logoutAll(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Bütün cihazlardan uğurla çıxış etdiniz'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Çıxış zamanı səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function me(Request $request)
    {
        try {
            $user = $this->userRepository->getUserWithRoles($request->user()->id);
            $permissions = $this->rolePermissionService->getUserPermissions($user->id);
            $statistics = $this->userRepository->getUserStatistics($user->id);

            return response()->json([
                'user' => new UserResource($user),
                'permissions' => $permissions,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Məlumatlar əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Doğrulama səhvləri',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Cari parol yanlışdır'
            ], 400);
        }

        try {
            $this->userRepository->update($user->id, [
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Parol uğurla dəyişdirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Parol dəyişdirilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Doğrulama səhvləri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->userRepository->update($request->user()->id, [
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return response()->json([
                'message' => 'Profil uğurla yeniləndi',
                'user' => new UserResource($user->load('roles'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Profil yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function deactivateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Doğrulama səhvləri',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Parol yanlışdır'
            ], 400);
        }

        try {
            $this->userRepository->update($user->id, [
                'status' => UserStatus::INACTIVE
            ]);

            $user->tokens()->delete();

            return response()->json([
                'message' => 'Hesabınız uğurla deaktiv edildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hesab deaktiv edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function sendEmailVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email artıq doğrulanmışdır'
            ], 400);
        }

        try {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Doğrulama emaili göndərildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Email göndərilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            $request->user()->currentAccessToken()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Token yeniləndi',
                'token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
