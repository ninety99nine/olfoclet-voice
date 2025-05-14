<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('role');
            $table->text('content');
            $table->json('context')->nullable();
            $table->foreignUuid('thread_id')->constrained('conversation_threads')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversation_messages');
    }
};
