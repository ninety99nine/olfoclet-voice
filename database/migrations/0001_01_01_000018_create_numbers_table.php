<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_active')->default(true);
            $table->string('name'); // e.g., "Sales Line"
            $table->string('number', 20)->unique(); // e.g., +1234567890
            $table->string('provider')->nullable(); // e.g., Africa's Talking
            $table->string('first_step')->nullable(); // e.g., "Playback" (type of the first step)
            $table->json('call_flow')->nullable(); // JSON object representing the call flow
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('numbers');
    }
};
