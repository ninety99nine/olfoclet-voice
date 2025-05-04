<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('copilot_user', function (Blueprint $table) {
            $table->primary(['copilot_id', 'user_id']);
            $table->foreignUuid('copilot_id')->constrained('copilots')->cascadeOnDelete()->index();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copilot_user');
    }
};
