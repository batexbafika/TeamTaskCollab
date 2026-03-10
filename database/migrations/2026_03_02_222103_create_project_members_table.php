<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id('membershipID'); // primary key

            $table->foreignId('projectID')->constrained('projects', 'projectID')->onDelete('cascade');

            $table->foreignId('userID')->constrained('users', 'userID')->onDelete('cascade');

            $table->enum('role', ['teamMember', 'administrator', 'projectManager']);
            $table->timestamp('joinedAt');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
