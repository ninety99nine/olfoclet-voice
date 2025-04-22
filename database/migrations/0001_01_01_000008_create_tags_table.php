<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('organization_id');
            $table->unique(['name', 'organization_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tags');
    }
};
