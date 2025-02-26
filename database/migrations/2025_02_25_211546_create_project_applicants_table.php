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
        Schema::create('project_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('freelancer_id');
            $table->text('message');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();


            // references freelancer_id from users table
            $table->foreign('freelancer_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_applicants');
    }
};
