<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Actions;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Modules\RolesPermissions\Support\PermissionName;

class GetDashboardQuickLinksAction
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(User $user): array
    {
        $links = [
            [
                'key' => 'users',
                'label' => 'Users',
                'description' => 'Kelola akun admin dan role.',
                'permission' => PermissionName::USERS_VIEW,
                'is_ready' => true,
                'url' => UserResource::getUrl('index'),
            ],
            [
                'key' => 'news',
                'label' => 'News',
                'description' => 'Kelola berita sekolah.',
                'permission' => PermissionName::NEWS_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'announcements',
                'label' => 'Announcements',
                'description' => 'Kelola pengumuman resmi.',
                'permission' => PermissionName::ANNOUNCEMENTS_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'galleries',
                'label' => 'Galleries',
                'description' => 'Kelola album galeri.',
                'permission' => PermissionName::GALLERIES_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'media',
                'label' => 'Media',
                'description' => 'Kelola library media.',
                'permission' => PermissionName::MEDIA_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'school_profile',
                'label' => 'School Profile',
                'description' => 'Perbarui profil sekolah.',
                'permission' => PermissionName::SCHOOL_PROFILE_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'theme',
                'label' => 'Theme Settings',
                'description' => 'Atur tema dan branding.',
                'permission' => PermissionName::THEME_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'navigation',
                'label' => 'Navigation',
                'description' => 'Kelola menu website.',
                'permission' => PermissionName::NAVIGATION_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
            [
                'key' => 'audit_logs',
                'label' => 'Audit Logs',
                'description' => 'Inspeksi aktivitas admin.',
                'permission' => PermissionName::AUDIT_VIEW,
                'is_ready' => false,
                'url' => null,
            ],
        ];

        return array_values(array_filter(
            $links,
            static fn (array $link): bool => $user->can($link['permission'])
        ));
    }
}
