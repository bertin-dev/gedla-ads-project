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
        Schema::create('validation_workflows', function (Blueprint $table) {
            $table->increments('id');
            $table->date('deadline')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['public', 'private', 'confidential'])->default('public');
            $table->unsignedInteger('workflow_sender')->comment('celui qui démarre et valide le workflow de validation');
            $table->unsignedInteger('workflow_receiver')->comment('celui qui recoit le workflow de validation dans son parapheur');
            $table->unsignedInteger('media_id')->comment('le fichier et le dossier pour lequel le processus de validation est enclenché');
            $table->string('message');
            $table->boolean('receive_mail_notification')->default(false)->comment('false=ne pas envoyer, true = envoyer');
            $table->boolean('receiver_read_doc')->default(false)->comment('false=document pas encore vu, true = document vu');
            $table->dateTime('receiver_read_doc_at')->nullable();
            $table->foreign('workflow_sender')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workflow_receiver')->references('id')->on('users')->onDelete('cascade');
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
