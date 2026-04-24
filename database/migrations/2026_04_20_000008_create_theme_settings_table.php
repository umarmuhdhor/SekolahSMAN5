<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_settings', static function (Blueprint $table): void {
            $table->id();
            $table->string('singleton_key')->default('default')->unique();
            $table->foreignId('logo_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('primary_color', 7)->nullable();
            $table->string('secondary_color', 7)->nullable();
            $table->string('accent_color', 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('logo_media_id');
            $table->index('is_active');
            $table->index('updated_by');
            $table->index('updated_at');
        });

        DB::statement('CREATE UNIQUE INDEX theme_settings_single_active_idx ON theme_settings (is_active) WHERE is_active = true');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS theme_settings_single_active_idx');
        Schema::dropIfExists('theme_settings');
    }
};
