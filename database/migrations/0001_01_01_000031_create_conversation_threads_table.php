<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationThreadsTable extends Migration
{
    public function up()
    {
        Schema::create('conversation_threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignUuid('copilot_id')->constrained()->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversation_threads');
    }
}
