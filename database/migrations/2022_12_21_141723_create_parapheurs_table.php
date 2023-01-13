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
        Schema::create('parapheurs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable()->comment('description of content');
            $table->unsignedInteger('project_id')->index()->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unsignedInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('media', function (Blueprint $table) {
            $table->unsignedInteger('parapheur_id')->index()->nullable();
            $table->foreign('parapheur_id')->references('id')->on('parapheurs')->onDelete('cascade');
        });

       /* Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('parapheur_id')->index()->nullable();
            $table->foreign('parapheur_id')->references('id')->on('parapheurs')->onDelete('cascade');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parapheurs');
    }
};
