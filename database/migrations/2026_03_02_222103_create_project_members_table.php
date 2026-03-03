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
    Schema::create('project_members', function (Blueprint $table) {
    $table->id('membershipID');
    $table->foreignId('projectID')->constrained('projects')->onDelete('cascade');
    $table->foreignId('userID')->constrained('users')->onDelete('cascade');
    $table->enum('role', ['teamMember', 'administrator', 'projectManager']);
    $table->timestamp('joinedAt')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
