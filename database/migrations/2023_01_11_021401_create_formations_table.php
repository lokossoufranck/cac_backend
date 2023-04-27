<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code")->nullable()->unique();
            $table->integer('order')->nullable()->unsigned();
            $table->integer('status')->unsigned();
            $table->string("lang")->nullable();
            $table->longtext("url_photo_1")->nullable();
            $table->longtext("url_photo_2")->nullable();
            $table->string("description_photo_1")->nullable();
            $table->string("description_photo_2")->nullable();
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
        Schema::dropIfExists('formations');
    }
}
