<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Call metadata
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', ['initiated', 'ringing', 'in-progress', 'completed', 'failed']);

            // Participants
            $table->string('from', 20); // Caller number
            $table->string('to', 20);   // Receiver number

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('hold_time')->nullable()->comment('Total hold time in seconds');
            $table->unsignedInteger('duration')->nullable()->comment('Total call duration in seconds');

            // Outcome
            $table->enum('disposition', ['resolved', 'escalated', 'callback', 'follow-up'])->nullable()->comment('Outcome of the call');

            // Transfers
            $table->unsignedInteger('transfer_count')->nullable()->default(0)->comment('Number of times the call was transferred');

            // Recording (if any)
            $table->string('recording_url')->nullable();
            $table->string('session_id')->nullable(); // from telco/IVR provider

            // Additional data (can be extended)
            $table->json('metadata')->nullable();

            // Agent notes
            $table->text('notes')->nullable()->comment('Agent notes for the call');

            // AI-generated fields
            $table->text('ai_summary')->nullable()->comment('AI-generated summary of the call');
            $table->json('ai_suggested_actions')->nullable()->comment('AI-suggested actions for the call');

            // Foreign keys
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('queue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            $table->timestamps();

            $table->index(['organization_id', 'agent_id']);
            $table->index(['direction', 'status']);
            $table->index(['from', 'to']);
            $table->index('contact_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
