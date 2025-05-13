<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_flows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g., "Customer Support Flow"
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_flows');
    }
};
