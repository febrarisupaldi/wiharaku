<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $roles = [
                [
                    'code' => 'super_admin',
                    'name' => 'Super Administrator',
                    'description' => 'Full system access.',
                ],
                [
                    'code' => 'admin',
                    'name' => 'Administrator',
                    'description' => 'Platform operational management.',
                ],
                [
                    'code' => 'moderator',
                    'name' => 'Moderator',
                    'description' => 'Content review and moderation.',
                ],
                [
                    'code' => 'contributor',
                    'name' => 'Contributor',
                    'description' => 'Registered content contributor.',
                ],
                [
                    'code' => 'vihara_manager',
                    'name' => 'Vihara Manager',
                    'description' => 'Manages assigned vihara.',
                ],
                [
                    'code' => 'organization_manager',
                    'name' => 'Organization Manager',
                    'description' => 'Manages assigned organizations.',
                ],
            ];

            foreach ($roles as $role) {
                DB::table('roles')->updateOrInsert(
                    ['code' => $role['code']],
                    [
                        'name' => $role['name'],
                        'description' => $role['description'],
                        'is_system' => 1,
                        'is_active' => 1,
                        'updated_at' => now(),
                    ]
                );
            }

            $permissions = [
                ['code' => 'vihara.view', 'module' => 'vihara', 'action' => 'view', 'name' => 'View Vihara'],
                ['code' => 'vihara.create', 'module' => 'vihara', 'action' => 'create', 'name' => 'Create Vihara'],
                ['code' => 'vihara.update', 'module' => 'vihara', 'action' => 'update', 'name' => 'Update Vihara'],
                ['code' => 'vihara.delete', 'module' => 'vihara', 'action' => 'delete', 'name' => 'Delete Vihara'],
                ['code' => 'vihara.review', 'module' => 'vihara', 'action' => 'review', 'name' => 'Review Vihara'],

                ['code' => 'photo.view', 'module' => 'photo', 'action' => 'view', 'name' => 'View Photos'],
                ['code' => 'photo.upload', 'module' => 'photo', 'action' => 'upload', 'name' => 'Upload Photos'],
                ['code' => 'photo.review', 'module' => 'photo', 'action' => 'review', 'name' => 'Review Photos'],
                ['code' => 'photo.delete', 'module' => 'photo', 'action' => 'delete', 'name' => 'Delete Photos'],
                ['code' => 'photo.set_primary', 'module' => 'photo', 'action' => 'set_primary', 'name' => 'Set Primary Photo'],

                ['code' => 'user.view', 'module' => 'user', 'action' => 'view', 'name' => 'View Users'],
                ['code' => 'user.manage', 'module' => 'user', 'action' => 'manage', 'name' => 'Manage Users'],

                ['code' => 'role.view', 'module' => 'role', 'action' => 'view', 'name' => 'View Roles'],
                ['code' => 'role.manage', 'module' => 'role', 'action' => 'manage', 'name' => 'Manage Roles'],

                ['code' => 'permission.manage', 'module' => 'permission', 'action' => 'manage', 'name' => 'Manage Permissions'],

                ['code' => 'region.view', 'module' => 'region', 'action' => 'view', 'name' => 'View Regions'],
                ['code' => 'region.manage', 'module' => 'region', 'action' => 'manage', 'name' => 'Manage Regions'],

                ['code' => 'audit.view', 'module' => 'audit', 'action' => 'view', 'name' => 'View Audit Logs'],
                [
                    'code' => 'organization.view',
                    'module' => 'organization',
                    'action' => 'view',
                    'name' => 'View Organizations',
                ],
                [
                    'code' => 'organization.create',
                    'module' => 'organization',
                    'action' => 'create',
                    'name' => 'Create Organizations',
                ],
                [
                    'code' => 'organization.update',
                    'module' => 'organization',
                    'action' => 'update',
                    'name' => 'Update Organizations',
                ],
                [
                    'code' => 'organization.delete',
                    'module' => 'organization',
                    'action' => 'delete',
                    'name' => 'Delete Organizations',
                ],
                [
                    'code' => 'organization.review',
                    'module' => 'organization',
                    'action' => 'review',
                    'name' => 'Review Organizations',
                ],
                [
                    'code' => 'organization.member.view',
                    'module' => 'organization',
                    'action' => 'member.view',
                    'name' => 'View Organization Members',
                ],
                [
                    'code' => 'organization.member.manage',
                    'module' => 'organization',
                    'action' => 'member.manage',
                    'name' => 'Manage Organization Members',
                ],
                [
                    'code' => 'organization.vihara.view',
                    'module' => 'organization',
                    'action' => 'vihara.view',
                    'name' => 'View Organization Viharas',
                ],
                [
                    'code' => 'organization.vihara.manage',
                    'module' => 'organization',
                    'action' => 'vihara.manage',
                    'name' => 'Manage Organization Vihara Relations',
                ],
            ];

            foreach ($permissions as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['code' => $permission['code']],
                    [
                        ...$permission,
                        'is_active' => 1,
                        'updated_at' => now(),
                    ]
                );
            }

            $this->syncRolePermissions();
        });
    }

    private function syncRolePermissions(): void
    {
        $matrix = [
            'super_admin' => '*',

            'admin' => [
                'vihara.view',
                'vihara.create',
                'vihara.update',
                'vihara.delete',
                'vihara.review',
                'photo.view',
                'photo.upload',
                'photo.review',
                'photo.delete',
                'photo.set_primary',
                'user.view',
                'user.manage',
                'role.view',
                'region.view',
                'region.manage',
                'audit.view',
            ],

            'moderator' => [
                'vihara.view',
                'vihara.review',
                'photo.view',
                'photo.upload',
                'photo.review',
                'photo.set_primary',
                'region.view',
                'audit.view',
            ],

            'contributor' => [
                'vihara.view',
                'vihara.create',
                'photo.view',
                'photo.upload',
                'region.view',
            ],

            'vihara_manager' => [
                'vihara.view',
                'vihara.update',
                'photo.view',
                'photo.upload',
                'photo.delete',
                'photo.set_primary',
                'region.view',
            ],
            'organization_manager' => [
                'organization.view',
                'organization.update',
                'organization.member.view',
                'organization.member.manage',
                'organization.vihara.view',
                'organization.vihara.manage',
                'photo.view',
                'photo.upload',
                'region.view',
            ],
        ];

        foreach ($matrix as $roleCode => $permissionCodes) {
            $roleId = DB::table('roles')
                ->where('code', $roleCode)
                ->value('id');

            if (!$roleId) {
                continue;
            }

            if ($permissionCodes === '*') {
                $permissionIds = DB::table('permissions')
                    ->where('is_active', 1)
                    ->pluck('id')
                    ->all();
            } else {
                $permissionIds = DB::table('permissions')
                    ->whereIn('code', $permissionCodes)
                    ->where('is_active', 1)
                    ->pluck('id')
                    ->all();
            }

            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
