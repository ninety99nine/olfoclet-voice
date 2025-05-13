<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_flow_node_media_file', function (Blueprint $table) {
            $table->primary(['call_flow_node_id', 'media_file_id']);
            $table->foreignUuid('call_flow_node_id')->constrained('call_flow_nodes')->cascadeOnDelete();
            $table->foreignUuid('media_file_id')->constrained('media_files')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_flow_node_media_file');
    }
};
