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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('taskID');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'inProgress', 'completed'])->default('open');
            $table->foreignId('createdBy')->constrained('users','userID')->onDelete('cascade');
            $table->foreignId('projectID')->constrained('projects','projectID')->onDelete('cascade');
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
