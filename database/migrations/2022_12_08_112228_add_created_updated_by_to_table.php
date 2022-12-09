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
        Schema::table('permissions', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->unsignedInteger('updated_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });


        Schema::table('roles', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->unsignedInteger('updated_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('user_alerts', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->unsignedInteger('updated_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });


        Schema::table('projects', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->unsignedInteger('updated_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('folders', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->unsignedInteger('updated_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('media', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->unsignedInteger('updated_by')->default('1');
            $table->unsignedInteger('signed_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('signed_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table){
            $table->unsignedInteger('created_by')->default('1');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
};
