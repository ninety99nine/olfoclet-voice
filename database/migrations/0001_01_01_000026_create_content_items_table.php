<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('locale')->nullable();
            $table->boolean('ai_ingested')->default(false);
            $table->boolean('copilot_enabled')->default(true);
            $table->boolean('ai_agent_enabled')->default(true);
            $table->boolean('help_center_enabled')->default(false);
            $table->enum('visibility', ['public', 'internal'])->default('internal');
            $table->enum('state', ['draft', 'active', 'archived'])->default('draft');
            $table->enum('type', ['folder', 'article', 'snippet', 'webpage'])->default('article');
            $table->foreignUuid('parent_id')->nullable()->constrained('content_items')->nullOnDelete()->index();
            $table->foreignUuid('source_id')->nullable()->constrained('content_sources')->nullOnDelete()->index();
            $table->foreignUuid('help_center_collection_id')->nullable()->constrained('help_center_collections')->nullOnDelete()->index();
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
