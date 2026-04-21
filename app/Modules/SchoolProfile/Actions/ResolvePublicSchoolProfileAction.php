<?php

declare(strict_types=1);

namespace App\Modules\SchoolProfile\Actions;

use App\Modules\SchoolProfile\Models\SchoolProfile;
use Illuminate\Support\Facades\Schema;

class ResolvePublicSchoolProfileAction
{
    /**
     * @return array{
     *     school_name: string,
     *     description: string,
     *     address: string,
     *     email: string|null,
     *     phone: string|null,
     *     website: string|null,
     *     facebook_url: string|null,
     *     instagram_url: string|null,
     *     youtube_url: string|null
     * }
     */
    public function execute(): array
    {
        if (! Schema::hasTable('school_profile')) {
            return [
                'school_name' => (string) config('app.name', 'CMS Sekolah Dinamis'),
                'description' => 'Portal informasi resmi sekolah untuk siswa, orang tua, dan masyarakat.',
                'address' => 'Alamat sekolah belum diperbarui.',
                'email' => null,
                'phone' => null,
                'website' => null,
                'facebook_url' => null,
                'instagram_url' => null,
                'youtube_url' => null,
            ];
        }

        $schoolProfile = SchoolProfile::query()->first();

        if ($schoolProfile === null) {
            return [
                'school_name' => (string) config('app.name', 'CMS Sekolah Dinamis'),
                'description' => 'Portal informasi resmi sekolah untuk siswa, orang tua, dan masyarakat.',
                'address' => 'Alamat sekolah belum diperbarui.',
                'email' => null,
                'phone' => null,
                'website' => null,
                'facebook_url' => null,
                'instagram_url' => null,
                'youtube_url' => null,
            ];
        }

        return [
            'school_name' => $this->normalizeText($schoolProfile->school_name, (string) config('app.name', 'CMS Sekolah Dinamis')),
            'description' => $this->normalizeText(
                $schoolProfile->description,
                'Portal informasi resmi sekolah untuk siswa, orang tua, dan masyarakat.'
            ),
            'address' => $this->normalizeText($schoolProfile->address, 'Alamat sekolah belum diperbarui.'),
            'email' => $this->normalizeEmail($schoolProfile->email),
            'phone' => $this->normalizeText($schoolProfile->phone),
            'website' => $this->normalizePublicUrl($schoolProfile->website),
            'facebook_url' => $this->normalizePublicUrl($schoolProfile->facebook_url),
            'instagram_url' => $this->normalizePublicUrl($schoolProfile->instagram_url),
            'youtube_url' => $this->normalizePublicUrl($schoolProfile->youtube_url),
        ];
    }

    private function normalizeText(?string $value, ?string $fallback = null): ?string
    {
        if ($value === null) {
            return $fallback;
        }

        $normalized = trim($value);

        return $normalized !== '' ? $normalized : $fallback;
    }

    private function normalizeEmail(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        return filter_var($normalized, FILTER_VALIDATE_EMAIL) !== false
            ? $normalized
            : null;
    }

    private function normalizePublicUrl(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        $scheme = parse_url($normalized, PHP_URL_SCHEME);

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return filter_var($normalized, FILTER_VALIDATE_URL) !== false
            ? $normalized
            : null;
    }
}
