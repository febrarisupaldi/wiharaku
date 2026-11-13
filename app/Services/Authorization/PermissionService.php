<?php

namespace App\Services\Authorization;

use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Memeriksa apakah user memiliki permission.
     *
     * $viharaId digunakan untuk membatasi akses
     * khusus Vihara Manager.
     */
    public function hasPermission(
        int $userId,
        string $permissionCode,
        ?int $viharaId = null
    ): bool {
        return DB::table('user_roles as ur')
            ->join('roles as r', 'r.id', '=', 'ur.role_id')
            ->join('role_permissions as rp', 'rp.role_id', '=', 'r.id')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('ur.user_id', $userId)
            ->where('r.is_active', 1)
            ->where('p.is_active', 1)
            ->where('p.code', $permissionCode)
            ->where(function ($query) use ($viharaId) {
                $query->where(function ($global) {
                    $global->where('r.code', '<>', 'vihara_manager');
                });

                if ($viharaId !== null) {
                    $query->orWhere(function ($manager) use ($viharaId) {
                        $manager->where('r.code', 'vihara_manager')
                            ->whereExists(function ($subQuery) use ($viharaId) {
                                $subQuery->selectRaw('1')
                                    ->from('vihara_user_access as vua')
                                    ->whereColumn('vua.user_id', 'ur.user_id')
                                    ->where('vua.vihara_id', $viharaId)
                                    ->where('vua.is_active', 1);
                            });
                    });
                }
            })->exists();
    }

    /**
     * Memeriksa apakah user memiliki role tertentu.
     */
    public function hasRole(
        int $userId,
        string $roleCode
    ): bool {
        return DB::table('user_roles as ur')
            ->join('roles as r', 'r.id', '=', 'ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.code', $roleCode)
            ->where('r.is_active', 1)
            ->exists();
    }

    /**
     * Mengambil seluruh role milik user.
     */
    public function getUserRoles(int $userId): array
    {
        return DB::table('user_roles as ur')
            ->join('roles as r', 'r.id', '=', 'ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.is_active', 1)
            ->orderBy('r.name')
            ->pluck('r.code')
            ->all();
    }

    /**
     * Mengambil seluruh permission user.
     */
    public function getUserPermissions(int $userId): array
    {
        return DB::table('user_roles as ur')
            ->join('roles as r', 'r.id', '=', 'ur.role_id')
            ->join('role_permissions as rp', 'rp.role_id', '=', 'r.id')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('ur.user_id', $userId)
            ->where('r.is_active', 1)
            ->where('p.is_active', 1)
            ->distinct()
            ->orderBy('p.code')
            ->pluck('p.code')
            ->all();
    }
}
