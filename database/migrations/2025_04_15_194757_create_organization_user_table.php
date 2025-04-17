<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['available', 'on call', 'on break', 'unavailable'])->default('available');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->primary(['organization_id', 'user_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
