<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * User service instance.
     *
     * @var UserService
     */
    private UserService $userService;

    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a paginated listing of users.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get paginated users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Sort by field", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_order", in="query", description="Sort order (asc/desc)", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 10);
            $filters = $request->only(['search', 'email_verified', 'sort_by', 'sort_order']);

            $users = $this->userService->getPaginatedUsers($filters, $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => new UserCollection($users)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve users');
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to create user');
        }
    }

    /**
     * Display the specified user.
     *
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get a specific user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->findUser((int) $id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => new UserResource($user)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve user');
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe Updated"),
     *             @OA\Property(property="email", type="string", example="john.updated@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->findUser((int) $id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $updatedUser = $this->userService->updateUser($user, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($updatedUser)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update user');
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = $this->userService->findUser((int) $id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->userService->deleteUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => null
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete user');
        }
    }

    /**
     * Get verified users.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/users/verified",
     *     summary="Get verified users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function verified(): JsonResponse
    {
        try {
            $users = $this->userService->getVerifiedUsers();

            return response()->json([
                'success' => true,
                'message' => 'Verified users retrieved successfully',
                'data' => UserResource::collection($users)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve verified users');
        }
    }

    /**
     * Get unverified users.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/users/unverified",
     *     summary="Get unverified users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function unverified(): JsonResponse
    {
        try {
            $users = $this->userService->getUnverifiedUsers();

            return response()->json([
                'success' => true,
                'message' => 'Unverified users retrieved successfully',
                'data' => UserResource::collection($users)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve unverified users');
        }
    }

    /**
     * Get user statistics.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/users/statistics",
     *     summary="Get user statistics",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->userService->getStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve statistics');
        }
    }

    /**
     * Update a user's role (Admin only).
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Patch(
     *     path="/api/admin/users/{id}/role",
     *     summary="Update user role (Admin only)",
     *     tags={"Admin - Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", enum={"user", "admin"}, example="admin")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role updated successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function updateRole(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'role' => 'required|string|in:user,admin'
            ]);

            $user = $this->userService->findUser((int) $id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Prevent changing own role
            if ($user->id === $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own role'
                ], Response::HTTP_FORBIDDEN);
            }

            $user->role = $request->role;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User role updated successfully',
                'data' => new UserResource($user)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update user role');
        }
    }

    /**
     * Get all admin users.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/users/admins",
     *     summary="Get all admin users (Admin only)",
     *     tags={"Admin - Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getAdmins(): JsonResponse
    {
        try {
            $admins = User::where('role', 'admin')->get();

            return response()->json([
                'success' => true,
                'message' => 'Admin users retrieved successfully',
                'data' => UserResource::collection($admins)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve admin users');
        }
    }

    /**
     * Handle exceptions and return appropriate JSON response.
     *
     * @param \Exception $exception
     * @param string $message
     * @return JsonResponse
     */
    private function handleException(\Exception $exception, string $message): JsonResponse
    {
        // Log the exception
        \Log::error($message, [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Return appropriate response based on exception type
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Generic error response
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? $exception->getMessage() : 'An error occurred'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
