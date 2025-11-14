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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('address', 200)->nullable();
            $table->foreignId('prefecture_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 200)->nullable();
            $table->text('opening_hours')->nullable();
            $table->enum('concept_type', ['maid', 'butler', 'idol', 'cosplay', 'other'])->default('other');
            $table->string('uniform_type', 50)->nullable();
            $table->string('image_url', 200)->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->timestamps();

            $table->index('prefecture_id');
            $table->index('city_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};

