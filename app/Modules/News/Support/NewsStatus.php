<?php

declare(strict_types=1);

namespace App\Modules\News\Support;

final class NewsStatus
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';

    public const ARCHIVED = 'archived';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::DRAFT,
            self::PUBLISHED,
            self::ARCHIVED,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function editableLabels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::ARCHIVED => 'Archived',
        ];
    }
}
