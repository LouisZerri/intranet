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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Informations personnelles
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Rôle et permissions
            $table->enum('role', ['collaborateur', 'manager', 'administrateur'])->default('collaborateur');
            $table->boolean('is_active')->default(true);

            // Informations professionnelles
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('localisation')->nullable();
            $table->string('position')->nullable();

            // Avatar
            $table->string('avatar')->nullable();

            // Hiérarchie
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');

            // Objectifs
            $table->decimal('revenue_target', 10, 2)->nullable()->comment('Objectif de CA mensuel');

            // Suivi connexion
            $table->timestamp('last_login_at')->nullable();

            // Laravel standards
            $table->rememberToken();
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('role');
            $table->index('department');
            $table->index('manager_id');
            $table->index('is_active');
            $table->index('email');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
