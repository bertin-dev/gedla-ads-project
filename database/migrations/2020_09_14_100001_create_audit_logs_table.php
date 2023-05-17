<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('media_id')->index()->nullable();
            $table->text('description')->nullable();
            $table->enum('operation_state',['success', 'pending', 'failed', 'rejected'])->comment('0=success, 1=pending, 2=failed, 3=rejected');
            $table->string('operation_type')->nullable();
            $table->string('message')->nullable();
            $table->unsignedInteger('user_id_sender')->index()->nullable();
            $table->unsignedInteger('user_id_receiver')->index()->nullable();
            $table->string('name')->nullable();
            $table->boolean('read_notification')->default(false);
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedInteger('current_user_id')->nullable();
            $table->text('properties')->nullable();
            $table->string('host', 46)->nullable();
            $table->boolean('receive_mail_notification')->default(false)->comment('false=ne pas envoyer, true = envoyer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
