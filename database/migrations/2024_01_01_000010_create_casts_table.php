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
        Schema::create('casts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->string('nickname', 50)->nullable();
            $table->integer('age')->nullable();
            $table->integer('height')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->text('hobby')->nullable();
            $table->text('special_skill')->nullable();
            $table->string('profile_image', 200)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casts');
    }
};

