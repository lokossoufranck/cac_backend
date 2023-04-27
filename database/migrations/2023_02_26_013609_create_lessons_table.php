<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string("code")->nullable()->unique();
            $table->string("title")->nullable();
            $table->string("short_description")->nullable();
            $table->string("descritpion")->nullable();
            $table->longtext("content")->nullable();
            $table->double("duration")->nullable();
            $table->longtext("data_photo_1")->nullable();
            $table->longtext("data_photo_2")->nullable();
            $table->longtext("url_photo_1")->nullable();
            $table->longtext("url_photo_2")->nullable();
            $table->longtext("description_photo_1")->nullable();
            $table->longtext("description_photo_2")->nullable();
            $table->integer('order')->nullable()->unsigned();
            $table->boolean('status')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->string("lang")->nullable(); 
            $table->integer('section_id')->unsigned();
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
        Schema::dropIfExists('modulefs');
    }
}
