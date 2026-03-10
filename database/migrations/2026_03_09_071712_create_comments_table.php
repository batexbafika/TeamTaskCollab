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
            Schema::create('comments', function (Blueprint $table) {
            $table->id('commentID');
            // FK to tasks.taskID
            $table->foreignId('taskID')->constrained('tasks', 'taskID')->onDelete('cascade');
            // FK to users.id
            $table->foreignId('createdBy')->constrained('users', 'userID')->onDelete('cascade');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
