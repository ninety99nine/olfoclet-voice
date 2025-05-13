<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('type', ['string', 'url', 'number', 'date']);
            $table->timestamps();

            $table->index('organization_id');
            $table->unique(['organization_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_attributes');
    }
};
