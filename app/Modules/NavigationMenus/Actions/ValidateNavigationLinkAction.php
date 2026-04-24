<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Actions;

use App\Modules\NavigationMenus\Support\NavigationLinkType;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class ValidateNavigationLinkAction
{
    /**
     * @throws ValidationException
     */
    public function execute(string $linkType, string $linkValue): string
    {
        $normalizedType = in_array($linkType, NavigationLinkType::all(), true)
            ? $linkType
            : NavigationLinkType::URL;

        $normalizedValue = trim($linkValue);

        if ($normalizedValue === '') {
            throw ValidationException::withMessages([
                'data.link_value' => 'Nilai link wajib diisi.',
            ]);
        }

        if ($normalizedType === NavigationLinkType::ROUTE) {
            if (! Route::has($normalizedValue)) {
                throw ValidationException::withMessages([
                    'data.link_value' => 'Route name tidak ditemukan.',
                ]);
            }

            return $normalizedValue;
        }

        $isInternalPath = str_starts_with($normalizedValue, '/');
        $isHttpUrl = filter_var($normalizedValue, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($normalizedValue, PHP_URL_SCHEME), ['http', 'https'], true);

        if (! $isInternalPath && ! $isHttpUrl) {
            throw ValidationException::withMessages([
                'data.link_value' => 'URL harus berupa path internal (/halaman) atau URL http/https yang valid.',
            ]);
        }

        return $normalizedValue;
    }
}
