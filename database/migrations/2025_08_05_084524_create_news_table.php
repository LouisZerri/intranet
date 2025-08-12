<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->enum('priority', ['normal', 'important', 'urgent'])->default('normal');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('target_roles')->nullable(); // ['collaborateur', 'manager', 'administrateur']
            $table->json('target_departments')->nullable(); // departments concernés
            $table->datetime('published_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->timestamps();
            
            $table->foreign('author_id')->references('id')->on('users');
            $table->index(['status', 'published_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('news');
    }
};