<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->boolean('active')->default(true)
                  ->comment('Allows temporary deactivation of the department without deleting it');
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade')->index();
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
