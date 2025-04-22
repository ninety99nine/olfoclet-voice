<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_active')->default(true);
            $table->string('name'); // e.g., "Sales Line"
            $table->string('number', 20)->unique(); // e.g., +1234567890
            $table->string('provider')->nullable(); // e.g., Africa's Talking
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('call_flow_id')->nullable()->constrained('call_flows')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('numbers');
    }
};
