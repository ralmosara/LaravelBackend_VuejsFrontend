<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Cache key prefix for users.
     */
    private const CACHE_PREFIX = 'users';

    /**
     * Cache duration in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Get paginated users with optional filtering and sorting.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['email_verified'])) {
            if ($filters['email_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get all users (cached).
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return Cache::remember(
            self::CACHE_PREFIX . '.all',
            self::CACHE_TTL,
            fn() => User::latest()->get()
        );
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUser(int $id): ?User
    {
        return Cache::remember(
            self::CACHE_PREFIX . ".{$id}",
            self::CACHE_TTL,
            fn() => User::find($id)
        );
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'user',
            ]);

            DB::commit();

            // Clear cache
            $this->clearCache();

            Log::info('User created', ['user_id' => $user->id]);

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        try {
            DB::beginTransaction();

            $updateData = array_filter([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'role' => $data['role'] ?? $user->role,
            ]);

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            DB::commit();

            // Clear cache
            $this->clearCache($user->id);

            Log::info('User updated', ['user_id' => $user->id]);

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        try {
            DB::beginTransaction();

            $userId = $user->id;
            $user->delete();

            DB::commit();

            // Clear cache
            $this->clearCache($userId);

            Log::info('User deleted', ['user_id' => $userId]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get verified users.
     *
     * @return Collection
     */
    public function getVerifiedUsers(): Collection
    {
        return User::whereNotNull('email_verified_at')
            ->orderBy('email_verified_at', 'desc')
            ->get();
    }

    /**
     * Get unverified users.
     *
     * @return Collection
     */
    public function getUnverifiedUsers(): Collection
    {
        return User::whereNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Clear user cache.
     *
     * @param int|null $userId
     * @return void
     */
    private function clearCache(?int $userId = null): void
    {
        Cache::forget(self::CACHE_PREFIX . '.all');
        Cache::forget(self::CACHE_PREFIX . '.stats');

        if ($userId) {
            Cache::forget(self::CACHE_PREFIX . ".{$userId}");
        }
    }

    /**
     * Get user statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . '.stats',
            self::CACHE_TTL,
            function () {
                return [
                    'total_users' => User::count(),
                    'verified_users' => User::whereNotNull('email_verified_at')->count(),
                    'unverified_users' => User::whereNull('email_verified_at')->count(),
                    'recent_users' => User::where('created_at', '>=', now()->subDays(7))->count(),
                    'admin_users' => User::where('role', 'admin')->count(),
                    'regular_users' => User::where('role', 'user')->count(),
                ];
            }
        );
    }

    /**
     * Get all admin users.
     *
     * @return Collection
     */
    public function getAdminUsers(): Collection
    {
        return User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Promote user to admin.
     *
     * @param User $user
     * @return User
     */
    public function promoteToAdmin(User $user): User
    {
        return $this->updateUser($user, ['role' => 'admin']);
    }

    /**
     * Demote admin to regular user.
     *
     * @param User $user
     * @return User
     */
    public function demoteToUser(User $user): User
    {
        return $this->updateUser($user, ['role' => 'user']);
    }
}