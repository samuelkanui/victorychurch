<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum values
        // This is a workaround since SQLite doesn't support ALTER COLUMN directly
        
        // First, check if we're using SQLite
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite: Recreate the table
            Schema::table('resources', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('resources', function (Blueprint $table) {
                $table->enum('type', ['file', 'link', 'video', 'document', 'audio'])->after('description');
            });
        } else {
            // MySQL/PostgreSQL: Use ALTER
            DB::statement("ALTER TABLE resources MODIFY COLUMN type ENUM('file', 'link', 'video', 'document', 'audio')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'sqlite') {
            Schema::table('resources', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('resources', function (Blueprint $table) {
                $table->enum('type', ['file', 'link', 'video', 'document'])->after('description');
            });
        } else {
            DB::statement("ALTER TABLE resources MODIFY COLUMN type ENUM('file', 'link', 'video', 'document')");
        }
    }
};
