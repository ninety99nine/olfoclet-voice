<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_flow_nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('call_flow_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., "Playback", "IVR", "Forward"
            $table->string('next_step')->nullable(); // ID of the next node
            $table->json('metadata'); // Node-specific settings (e.g., message, options)
            $table->json('position'); // Position for Vue Flow (e.g., { x: 100, y: 100 })
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_flow_nodes');
    }
};
