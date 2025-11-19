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
        Schema::table('prayer_requests', function (Blueprint $table) {
            // Add privacy field to replace is_public
            $table->enum('privacy', ['public', 'group', 'private'])->default('public')->after('status');
            
            // Add leader response fields
            $table->text('leader_response')->nullable()->after('answered_at');
            $table->timestamp('responded_at')->nullable()->after('leader_response');
            $table->foreignId('responded_by')->nullable()->constrained('users')->after('responded_at');
            
            // Add moderation fields
            $table->text('moderation_note')->nullable()->after('responded_by');
            $table->timestamp('moderated_at')->nullable()->after('moderation_note');
            $table->foreignId('moderated_by')->nullable()->constrained('users')->after('moderated_at');
            
            // Update status enum to include more options
            $table->enum('status', ['pending', 'active', 'answered', 'flagged', 'resolved', 'closed'])->default('active')->change();
            
            // Add indexes for new fields
            $table->index(['privacy', 'status']);
            $table->index('responded_by');
            $table->index('moderated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_requests', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'privacy',
                'leader_response',
                'responded_at',
                'responded_by',
                'moderation_note',
                'moderated_at',
                'moderated_by'
            ]);
            
            // Revert status enum
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending')->change();
        });
    }
};
