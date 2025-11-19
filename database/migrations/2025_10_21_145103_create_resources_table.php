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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'link', 'video', 'document']);
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('external_url')->nullable(); // For external links
            $table->string('file_name')->nullable(); // Original file name
            $table->string('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable(); // File MIME type
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('visibility', ['group', 'public'])->default('group');
            $table->boolean('is_active')->default(true);
            $table->json('categories')->nullable(); // Tags/categories
            $table->integer('download_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['group_id', 'is_active']);
            $table->index(['visibility', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
