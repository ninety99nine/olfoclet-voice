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
        Schema::create('copilot_knowledge_base', function (Blueprint $table) {
            $table->primary(['copilot_id', 'knowledge_base_id']);
            $table->foreignUuid('copilot_id')->constrained('copilots')->cascadeOnDelete();
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copilot_knowledge_base');
    }
};
