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
            $table->string('collection_name')->index();
            $table->string('name')->index();
            $table->string('file_name');
            $table->string('mime_type')->nullable()->index();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size')->index();
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->unsignedInteger('version')->default(0)->index();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('state', ['unlocked', 'locked'])->default('unlocked')->comment('unlocked=0, locked=1');
            //$table->boolean('signing')->default(false)->comment('false=not signed, true=signed');
            $table->boolean('archived')->default(false)->comment('false=unarchived, true=archived');
            $table->integer('statut')->default(0)->comment('En attente de validation');
            $table->dateTime('global_deadline')->nullable();
            $table->nullableTimestamps();
        });
    }
};
