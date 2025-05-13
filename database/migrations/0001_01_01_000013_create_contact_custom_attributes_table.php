<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_custom_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('custom_attribute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('type', ['string', 'url', 'number', 'date']);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index('contact_id');
            $table->index('custom_attribute_id');
            $table->unique(['contact_id', 'custom_attribute_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_custom_attributes');
    }
};
