<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('call_id')->constrained('calls')->cascadeOnDelete();
            $table->enum('activity_type', [
                'call started', 'call answered', 'call ended',
                'status changed',
                'transfer initiated', 'transfer accepted', 'transfer rejected', 'transfer completed',
                'outcome determined',
                'hold started', 'hold ended',
                'notes taken',
                'ai summary generated',
                'queue entered', 'queue exited',
                'recording started', 'recording stopped',
                'ivr interaction'
            ]);
            $table->text('description')->nullable();
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('performed_at')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('call_id');
            $table->index('performed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_activities');
    }
};
