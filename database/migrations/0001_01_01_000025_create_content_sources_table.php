<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // "telcoflo", "zendesk", "guru", "notion", "confluence", "website"
            $table->string('name'); // "Notion (Project A)", "help.example.io/en"
            $table->timestamp('last_synced_at')->nullable();
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_sources');
    }
};
