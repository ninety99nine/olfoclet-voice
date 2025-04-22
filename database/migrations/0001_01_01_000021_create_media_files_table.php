<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g., "Welcome Message"
            $table->string('file_name'); // e.g., "welcome_message.mp3"
            $table->string('mime_type'); // e.g., "audio/mpeg"
            $table->string('path'); // S3 path (e.g., "media/welcome_message.mp3")
            $table->unsignedBigInteger('size'); // File size in bytes
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_files');
    }
};
