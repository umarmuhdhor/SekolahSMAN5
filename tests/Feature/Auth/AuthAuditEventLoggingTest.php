<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Modules\AuditLogs\Models\AuditLog;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAuditEventLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan listener auth menulis event login/logout/failed ke audit table terstruktur.
     */
    public function test_auth_events_are_written_to_audit_logs_table(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        event(new Login('web', $user, false));
        event(new Failed('web', $user, ['email' => $user->email, 'password' => 'secret-password']));
        event(new Logout('web', $user));

        $this->assertDatabaseCount('audit_logs', 3);

        $eventTypes = AuditLog::query()->orderBy('id')->pluck('event_type')->all();
        $this->assertSame([
            AuditEventType::LOGIN,
            AuditEventType::FAILED_LOGIN,
            AuditEventType::LOGOUT,
        ], $eventTypes);

        $failedLoginAudit = AuditLog::query()
            ->where('event_type', AuditEventType::FAILED_LOGIN)
            ->firstOrFail();

        $this->assertSame(AuditModule::AUTH, $failedLoginAudit->module);
        $this->assertNull(data_get($failedLoginAudit->after_json, 'context.credentials.password'));
        $this->assertSame($user->email, data_get($failedLoginAudit->after_json, 'email'));
    }
}
