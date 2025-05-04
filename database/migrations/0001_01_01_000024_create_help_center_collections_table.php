<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_center_collections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Name of the collection (e.g., "General Help", "Billing")
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->onDelete('cascade')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_center_collections');
    }
};
