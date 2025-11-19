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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Rename points to grade
            $table->renameColumn('points', 'grade');
            
            // Add status column
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted')->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Rename grade back to points
            $table->renameColumn('grade', 'points');
            
            // Drop status column
            $table->dropColumn('status');
        });
    }
};
