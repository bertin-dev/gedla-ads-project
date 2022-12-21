<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable()->comment('description of content');
            $table->boolean('functionality')->default(false)->comment('if functionality is true, so all users will seen folder as card content functionality');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
