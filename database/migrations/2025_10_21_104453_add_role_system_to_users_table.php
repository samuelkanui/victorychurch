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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'leader', 'member'])->default('member')->after('email_verified_at');
            $table->json('capabilities')->nullable()->after('role');
            $table->string('profile_photo_path')->nullable()->after('capabilities');
            $table->string('google_id')->nullable()->after('profile_photo_path');
            $table->timestamp('last_login_at')->nullable()->after('google_id');
            $table->boolean('is_active')->default(true)->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'capabilities',
                'profile_photo_path',
                'google_id',
                'last_login_at',
                'is_active'
            ]);
        });
    }
};
