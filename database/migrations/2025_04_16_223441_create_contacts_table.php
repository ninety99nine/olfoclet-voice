<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('phone_number', 20)->unique();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('organization_id');
            $table->index('phone_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
