<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_flow_node_media_file', function (Blueprint $table) {
            $table->uuid('call_flow_node_id');
            $table->uuid('media_file_id');
            $table->primary(['call_flow_node_id', 'media_file_id']);
            $table->foreign('call_flow_node_id')->references('id')->on('call_flow_nodes')->onDelete('cascade');
            $table->foreign('media_file_id')->references('id')->on('media_files')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_flow_node_media_file');
    }
};
