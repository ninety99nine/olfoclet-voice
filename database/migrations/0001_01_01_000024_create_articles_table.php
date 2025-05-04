<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('content');
            $table->boolean('ai_searchable')->default(true);
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete()->index();
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
