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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');
            $table->timestamps();

            $table->index(['shop_id', 'user_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->onDelete('cascade');
            $table->enum('sender_type', ['user', 'shop_admin']);
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->enum('message_type', ['text', 'image', 'file'])->default('text');
            $table->string('file_path', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('room_id');
            $table->index('created_at');
        });

        Schema::create('chat_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->onDelete('cascade');
            $table->enum('recipient_type', ['user', 'shop_admin']);
            $table->unsignedBigInteger('recipient_id');
            $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['recipient_type', 'recipient_id']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_notifications');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_rooms');
    }
};

