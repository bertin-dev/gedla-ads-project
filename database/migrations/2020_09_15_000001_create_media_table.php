<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');

            $table->morphs('model');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->unsignedInteger('media_version')->nullable()->index();
            $table->enum('media_status', ['public', 'private', 'confidential'])->default('public');
            $table->enum('media_state', ['unlocked', 'locked'])->default('unlocked')->comment('unlocked=0, locked=1');
            $table->enum('media_signing', ['0', '1'])->default('0')->comment('0=non signe, 1=signé');
            $table->enum('media_save', ['unarchived', 'archived'])->default('unarchived')->comment('0=non-archivé, 1=archivé');

            $table->nullableTimestamps();
        });
    }
};
