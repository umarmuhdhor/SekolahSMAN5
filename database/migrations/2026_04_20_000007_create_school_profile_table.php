<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_profile', static function (Blueprint $table): void {
            $table->id();
            $table->string('singleton_key')->default('default')->unique();
            $table->string('school_name');
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('website')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('updated_by');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profile');
    }
};
