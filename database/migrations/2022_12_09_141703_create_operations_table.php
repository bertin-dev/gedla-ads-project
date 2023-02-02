<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->increments('id');
            $table->date('deadline')->nullable();
            $table->string('operation_type');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['public', 'private', 'confidential'])->default('public');
            $table->unsignedInteger('user_id_sender')->index()->nullable()->comment('celui qui démarre qui démarre l\'opération ');
            $table->unsignedInteger('user_id_receiver')->index()->nullable()->comment('celui qui recoit');
            $table->unsignedInteger('media_id')->nullable()->comment('le fichier et le dossier pour lequel le processus de validation est enclenché');
            $table->string('message')->nullable();
            $table->enum('operation_state',['success', 'pending', 'failed'])->comment('0=failed and 1=success, 2=pending');
            $table->string('num_operation')->nullable();
            $table->boolean('receive_mail_notification')->default(false)->comment('false=ne pas envoyer, true = envoyer');
            $table->boolean('receiver_read_doc')->default(false)->comment('false=document pas encore vu, true = document vu');
            $table->dateTime('receiver_read_doc_at')->nullable();
            $table->foreign('user_id_sender')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_receiver')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');

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
        Schema::dropIfExists('validation_workflows');
    }
};
