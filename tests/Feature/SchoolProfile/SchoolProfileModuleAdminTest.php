<?php

declare(strict_types=1);

namespace Tests\Feature\SchoolProfile;

use App\Filament\Resources\SchoolProfiles\Pages\CreateSchoolProfile;
use App\Filament\Resources\SchoolProfiles\Pages\EditSchoolProfile;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\RolesPermissions\Support\RoleName;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolProfileModuleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan super admin dapat membuat dan memperbarui profil sekolah dengan audit update tercatat.
     */
    public function test_super_admin_can_create_and_update_school_profile_with_audit_log(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateSchoolProfile::class)
            ->set('data.school_name', 'SMA Dinamis Nusantara')
            ->set('data.description', 'Sekolah unggulan berbasis karakter dan teknologi.')
            ->set('data.address', 'Jl. Pendidikan No. 1, Makassar')
            ->set('data.email', 'profil@sekolah.local')
            ->set('data.phone', '+6281234567890')
            ->set('data.website', 'https://sekolah.local')
            ->call('create');

        $profile = SchoolProfile::query()->firstOrFail();

        Livewire::test(EditSchoolProfile::class, ['record' => $profile->getRouteKey()])
            ->set('data.phone', '+6281999999999')
            ->call('save');

        $profile->refresh();

        $this->assertSame('+6281999999999', $profile->phone);
        $this->assertSame($superAdmin->id, $profile->updated_by);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::SCHOOL_PROFILE_UPDATE,
            'module' => AuditModule::SCHOOL_PROFILE,
            'entity_type' => SchoolProfile::class,
            'entity_id' => (string) $profile->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan validasi kontak menolak email dan telepon tidak valid.
     */
    public function test_contact_validation_rejects_invalid_email_and_phone(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateSchoolProfile::class)
            ->set('data.school_name', 'SMA Dinamis Nusantara')
            ->set('data.address', 'Jl. Pendidikan No. 1, Makassar')
            ->set('data.email', 'email-tidak-valid')
            ->set('data.phone', 'abcde')
            ->call('create')
            ->assertHasErrors(['data.email', 'data.phone']);
    }

    /**
     * Memastikan pola single-record menolak pembuatan profil kedua dari backend.
     */
    public function test_single_record_pattern_blocks_second_profile_creation(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        SchoolProfile::factory()->create([
            'singleton_key' => 'default',
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(CreateSchoolProfile::class)
            ->set('data.school_name', 'SMA Kedua')
            ->set('data.address', 'Jl. Lain')
            ->set('data.email', 'kedua@sekolah.local')
            ->set('data.phone', '+6281234500000')
            ->call('create')
            ->assertHasErrors(['data.school_name']);

        $this->assertDatabaseCount('school_profile', 1);
    }

    /**
     * Memastikan enforce policy sekolah profil untuk role berizin dan tidak berizin.
     */
    public function test_school_profile_policy_enforces_view_and_update_permissions(): void
    {
        $profile = SchoolProfile::factory()->create();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAny', SchoolProfile::class));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $profile));

        $this->assertFalse(Gate::forUser($guru)->allows('viewAny', SchoolProfile::class));
        $this->assertFalse(Gate::forUser($guru)->allows('update', $profile));
    }

    /**
     * Memastikan user tanpa permission school_profile.* ditolak backend saat akses halaman profil.
     */
    public function test_user_without_school_profile_permission_cannot_access_pages(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($guru)
            ->get('/admin/school-profile')
            ->assertForbidden();

        $this->actingAs($guru)
            ->get('/admin/school-profile/create')
            ->assertForbidden();
    }
}
