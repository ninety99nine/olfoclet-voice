<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic info
            $table->string('name', 100);
            $table->string('description', 255)->nullable();

            $table->boolean('active')->default(true)
                  ->comment('Allows temporary deactivation of the queue without deleting it');

            // Ownership
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();

            // SLA Settings
            $table->unsignedInteger('sla_threshold')->nullable()
                  ->comment('Max time (seconds) to answer a call and still meet SLA');
            $table->unsignedInteger('max_wait_time')->nullable()
                  ->comment('Absolute timeout (seconds) before fallback action is triggered');
            $table->unsignedInteger('escalation_threshold')->nullable()
                  ->comment('Time (seconds) after which call is flagged for escalation');

            // Performance Metrics (calculated over time)
            $table->unsignedInteger('avg_wait_time')->nullable()
                  ->comment('Rolling average wait time in seconds');
            $table->decimal('service_level', 5, 2)->nullable()
                  ->comment('Percentage of calls answered within SLA threshold');
            $table->decimal('abandonment_rate', 5, 2)->nullable()
                  ->comment('Percentage of calls abandoned before being answered');
            $table->timestamp('last_sla_review')->nullable()
                  ->comment('Last time SLA metrics were reviewed or updated');

            // Call volume thresholds
            $table->unsignedInteger('call_volume_warning_threshold')->nullable()
                  ->comment('Number of waiting calls triggering a warning state');
            $table->unsignedInteger('call_volume_critical_threshold')->nullable()
                  ->comment('Number of waiting calls triggering a critical state');

            // Routing logic
            $table->boolean('record_calls')->default(true);
            $table->enum('strategy', ['round robin', 'ring all', 'least calls', 'longest idle', 'random'])->default('round-robin');
            $table->enum('priority_level', ['normal', 'vip'])->default('normal')
                  ->comment('Priority level for queuing, e.g., VIP customers skip the queue');
            $table->json('metadata')->nullable()
                  ->comment('Additional routing rules, e.g., agent skills, CRM data');

            // Wait time enhancements
            $table->string('hold_music_url')->nullable()
                  ->comment('URL for hold music played during wait');
            $table->text('greeting_message')->nullable()
                  ->comment('Personalized greeting message for callers');
            $table->text('wait_message')->nullable()
                  ->comment('Message played during wait, e.g., estimated wait time');
            $table->boolean('callback_enabled')->default(false)
                  ->comment('Allow customers to request a callback instead of waiting');

            // Optional fallback handling
            $table->foreignUuid('fallback_queue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('fallback_department_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            // Indexes for optimized lookups
            $table->index('organization_id');
            $table->index('department_id');
            $table->index('sla_threshold');
            $table->index('last_sla_review');
        });
    }

    public function down()
    {
        Schema::dropIfExists('queues');
    }
};
