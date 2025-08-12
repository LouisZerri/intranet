<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category')->nullable();
            $table->string('level')->default('debutant'); // debutant, intermediaire, avance
            $table->integer('duration_hours')->default(0);
            $table->decimal('cost', 8, 2)->nullable();
            $table->string('provider')->nullable();
            $table->string('format')->default('presentiel'); // presentiel, distanciel, hybride
            $table->integer('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('prerequisites')->nullable();
            $table->json('objectives')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};