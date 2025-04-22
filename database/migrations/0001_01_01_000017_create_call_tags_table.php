<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_tag', function (Blueprint $table) {
            $table->foreignUuid('call_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['call_id', 'tag_id']);
            $table->index('tag_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_tag');
    }
};
