<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omni_channel_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('channel', [
                'whatsapp', 'instagram', 'telegram', 'messenger', 'facebook',
                'wechat', 'line', 'sms'
            ]);
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->string('from', 50);
            $table->string('to', 50);
            $table->text('content');
            $table->string('message_type', 50)->nullable(); // e.g., text, template, media
            $table->string('external_message_id')->nullable(); // e.g., WhatsApp message ID
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->cascadeOnDelete();
            $table->foreignUuid('agent_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('queue_id')->nullable()->constrained('queues')->cascadeOnDelete();
            $table->timestamps();

            $table->index('channel');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omni_channel_messages');
    }
};
