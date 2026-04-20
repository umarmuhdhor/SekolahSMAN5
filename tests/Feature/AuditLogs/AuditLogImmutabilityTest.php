<?php

declare(strict_types=1);

namespace Tests\Feature\AuditLogs;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AuditLogImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan record audit log tidak dapat diupdate setelah tersimpan.
     */
    public function test_audit_log_record_cannot_be_updated(): void
    {
        $auditLog = AuditLog::query()->create([
            'event_type' => 'test_event',
            'module' => 'test_module',
            'created_at' => now(),
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Audit log is immutable and cannot be updated.');

        $auditLog->update(['module' => 'changed_module']);
    }

    /**
     * Memastikan record audit log tidak dapat dihapus setelah tersimpan.
     */
    public function test_audit_log_record_cannot_be_deleted(): void
    {
        $auditLog = AuditLog::query()->create([
            'event_type' => 'test_event',
            'module' => 'test_module',
            'created_at' => now(),
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Audit log is immutable and cannot be deleted.');

        $auditLog->delete();
    }

    /**
     * Memastikan payload sensitif disanitasi sebelum disimpan.
     */
    public function test_sensitive_payload_is_redacted_before_persisted(): void
    {
        app(RecordAuditLogAction::class)->execute(
            eventType: 'test_event',
            module: 'test_module',
            after: [
                'password' => 'plaintext',
                'profile' => [
                    'api_key' => 'secret-value',
                    'normal_value' => 'allowed',
                ],
            ],
            metadata: [
                'authorization' => 'Bearer token-value',
            ],
        );

        $auditLog = AuditLog::query()->firstOrFail();

        $this->assertSame('[REDACTED]', data_get($auditLog->after_json, 'password'));
        $this->assertSame('[REDACTED]', data_get($auditLog->after_json, 'profile.api_key'));
        $this->assertSame('allowed', data_get($auditLog->after_json, 'profile.normal_value'));
        $this->assertSame('[REDACTED]', data_get($auditLog->metadata, 'authorization'));
    }
}
