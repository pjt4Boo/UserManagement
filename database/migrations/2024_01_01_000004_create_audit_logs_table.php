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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // created, updated, deleted, deactivated, etc.
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('changes')->nullable(); // stores before/after values
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('actor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
