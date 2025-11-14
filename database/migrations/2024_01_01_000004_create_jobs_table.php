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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->enum('job_type', ['part_time', 'full_time', 'contract'])->default('part_time');
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->text('work_hours')->nullable();
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->enum('gender_requirement', ['male', 'female', 'any'])->default('any');
            $table->integer('age_min')->nullable();
            $table->integer('age_max')->nullable();
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->date('application_deadline')->nullable();
            $table->timestamps();

            $table->index('shop_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

