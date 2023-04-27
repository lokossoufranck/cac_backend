<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulefs', function (Blueprint $table) {
            $table->id();
            $table->string("code")->nullable()->unique();
            $table->string("title")->nullable();
            $table->string("short_description")->nullable();
            $table->string("descritpion")->nullable();
            $table->double("prix")->nullable();
            $table->double("duration")->nullable();
            $table->string("level")->nullable();
            $table->boolean("has_certification")->nullable();
            $table->longtext("data_photo_1")->nullable();
            $table->longtext("data_photo_2")->nullable();
            $table->longtext("data_photo_3")->nullable();
            $table->longtext("url_photo_1")->nullable();
            $table->longtext("url_photo_2")->nullable();
            $table->longtext("url_photo_3")->nullable();
            $table->longtext("description_photo_1")->nullable();
            $table->longtext("description_photo_2")->nullable();
            $table->longtext("description_photo_3")->nullable();
            $table->integer('order')->nullable()->unsigned();
            $table->boolean('status')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->string("lang")->nullable(); 
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
