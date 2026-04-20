<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Actions;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PrepareMediaAssetPayloadAction
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function execute(array $data): array
    {
        $path = (string) ($data['path'] ?? '');

        if (blank($path)) {
            throw ValidationException::withMessages([
                'data.path' => 'File media wajib diunggah.',
            ]);
        }

        $diskName = (string) config('media_library.disk', config('filesystems.default'));
        $disk = Storage::disk($diskName);
        $bucket = config("filesystems.disks.{$diskName}.bucket");

        if (! $disk->exists($path)) {
            throw ValidationException::withMessages([
                'data.path' => 'File media tidak ditemukan di storage.',
            ]);
        }

        $mimeType = (string) $disk->mimeType($path);
        $sizeBytes = (int) $disk->size($path);

        if (! $this->isAllowedMimeType($mimeType)) {
            throw ValidationException::withMessages([
                'data.path' => 'Tipe file tidak diizinkan.',
            ]);
        }

        $maxSizeKb = $this->resolveMaxSizeKbForMimeType($mimeType);

        if ($sizeBytes > ($maxSizeKb * 1024)) {
            throw ValidationException::withMessages([
                'data.path' => "Ukuran file melebihi batas {$maxSizeKb} KB untuk tipe {$mimeType}.",
            ]);
        }

        $fileName = basename($path);
        $originalName = $this->sanitizeOriginalName((string) ($data['original_name'] ?? $fileName));

        return [
            ...$data,
            'disk' => $diskName,
            'bucket' => filled($bucket) ? (string) $bucket : null,
            'path' => $path,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'extension' => pathinfo($fileName, PATHINFO_EXTENSION) ?: null,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'checksum' => $this->calculateSha256Checksum($disk, $path),
            'visibility' => (string) config('media_library.visibility', 'private'),
        ];
    }

    private function isAllowedMimeType(string $mimeType): bool
    {
        $allowedMimeTypes = (array) config('media_library.allowed_mime_types', []);

        foreach ($allowedMimeTypes as $allowedMimeType) {
            if ($this->matchesPattern($mimeType, (string) $allowedMimeType)) {
                return true;
            }
        }

        return false;
    }

    private function resolveMaxSizeKbForMimeType(string $mimeType): int
    {
        /** @var array<string, int> $rules */
        $rules = (array) config('media_library.max_size_kb', []);

        foreach ($rules as $pattern => $sizeKb) {
            if ($pattern === '*') {
                continue;
            }

            if ($this->matchesPattern($mimeType, (string) $pattern)) {
                return (int) $sizeKb;
            }
        }

        return (int) ($rules['*'] ?? 10240);
    }

    private function matchesPattern(string $value, string $pattern): bool
    {
        if (! Str::contains($pattern, '*')) {
            return $value === $pattern;
        }

        $prefix = Str::before($pattern, '*');

        return Str::startsWith($value, $prefix);
    }

    private function sanitizeOriginalName(string $originalName): string
    {
        $safeName = basename(str_replace('\\', '/', $originalName));

        return Str::limit($safeName, 255, '');
    }

    /**
     * @throws ValidationException
     */
    private function calculateSha256Checksum(Filesystem $disk, string $path): string
    {
        $stream = $disk->readStream($path);

        if ($stream === false) {
            throw ValidationException::withMessages([
                'data.path' => 'Gagal membaca file untuk verifikasi checksum.',
            ]);
        }

        $hashContext = hash_init('sha256');
        hash_update_stream($hashContext, $stream);
        fclose($stream);

        return hash_final($hashContext);
    }
}
