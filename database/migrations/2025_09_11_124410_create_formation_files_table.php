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
        Schema::create('formation_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size'); // Taille en bytes
            $table->enum('type', ['document', 'video', 'audio', 'image', 'archive', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['formation_id', 'type']);
            $table->index(['formation_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_files');
    }
};