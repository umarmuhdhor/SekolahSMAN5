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
        Schema::create('navigation_menus', static function (Blueprint $table): void {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->string('location', 32)->default('header');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('location');
            $table->index('is_active');
            $table->index('updated_by');
            $table->index('updated_at');
        });

        Schema::create('navigation_items', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('link_type', 16)->default('url');
            $table->string('link_value');
            $table->string('target', 16)->default('_self');
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('navigation_menu_id');
            $table->index('parent_id');
            $table->index('is_visible');
            $table->index('updated_by');
            $table->index('updated_at');
        });

        DB::statement('CREATE UNIQUE INDEX navigation_items_unique_root_order_idx ON navigation_items (navigation_menu_id, sort_order) WHERE parent_id IS NULL');
        DB::statement('CREATE UNIQUE INDEX navigation_items_unique_child_order_idx ON navigation_items (navigation_menu_id, parent_id, sort_order) WHERE parent_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS navigation_items_unique_root_order_idx');
        DB::statement('DROP INDEX IF EXISTS navigation_items_unique_child_order_idx');

        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
    }
};
