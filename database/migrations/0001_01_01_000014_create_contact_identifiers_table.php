<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_identifiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contact_id')->constrained()->cascadeOnDelete()->index();
            $table->enum('type', ['phone', 'email', 'external id']);
            $table->string('value', 255); // e.g., +1234567890, john.doe@example.com, 1234-5678-9123
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('contact_id');
            $table->index(['type', 'value']);
            $table->unique(['contact_id', 'type', 'value']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_identifiers');
    }
};
