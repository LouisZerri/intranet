<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('en_attente'); // en_attente, approuve, refuse, termine
            $table->text('motivation')->nullable();
            $table->text('manager_comments')->nullable();
            $table->string('priority')->default('normale'); // basse, normale, haute
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->datetime('requested_at');
            $table->datetime('approved_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->decimal('final_cost', 8, 2)->nullable();
            $table->integer('hours_completed')->default(0);
            $table->text('feedback')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_requests');
    }
};