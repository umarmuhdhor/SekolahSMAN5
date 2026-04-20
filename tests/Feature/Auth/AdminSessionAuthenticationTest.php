<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Filament\Auth\Pages\Login as AdminLoginPage;
use App\Models\User;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSessionAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Menyiapkan panel Filament dan state rate limiter sebelum setiap skenario auth diuji.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        RateLimiter::clear($this->authRateLimitKey());
    }

    /**
     * Memastikan panel admin tetap berbasis session auth: guest tidak boleh mengakses dashboard admin.
     */
    public function test_guest_is_redirected_to_admin_login_page(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    /**
     * Memastikan user admin valid dapat login melalui halaman auth Filament.
     */
    public function test_admin_can_authenticate_through_filament_login_page(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $user->assignRole(RoleName::SUPER_ADMIN);

        Livewire::test(AdminLoginPage::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'password')
            ->call('authenticate');

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Memastikan login gagal men-dispatch event Failed dan menghasilkan feedback validasi.
     */
    public function test_failed_login_dispatches_failed_event(): void
    {
        Event::fake([Failed::class]);

        $user = User::factory()->create([
            'password' => 'password',
        ]);

        Livewire::test(AdminLoginPage::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'invalid-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        Event::assertDispatched(Failed::class);
    }

    /**
     * Memastikan throttling login aktif dan dapat dikonfigurasi melalui auth_session.
     *
     * Skenario:
     * - Limit percobaan di-set 1.
     * - Percobaan gagal pertama men-dispatch event Failed.
     * - Percobaan kedua diblokir throttle sehingga event Failed tidak bertambah.
     */
    public function test_login_attempts_are_throttled_based_on_configured_limit(): void
    {
        Event::fake([Failed::class]);

        config()->set('auth_session.login_max_attempts', 1);
        config()->set('auth_session.login_decay_seconds', 120);

        $user = User::factory()->create([
            'password' => 'password',
        ]);

        Livewire::test(AdminLoginPage::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'invalid-password')
            ->call('authenticate');

        Livewire::test(AdminLoginPage::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'invalid-password')
            ->call('authenticate');

        Event::assertDispatchedTimes(Failed::class, 1);
    }

    /**
     * Menghasilkan key rate limiter yang dipakai Livewire rate limiting pada metode authenticate.
     */
    private function authRateLimitKey(string $ip = '127.0.0.1'): string
    {
        return 'livewire-rate-limiter:'.sha1(AdminLoginPage::class.'|authenticate|'.$ip);
    }
}
