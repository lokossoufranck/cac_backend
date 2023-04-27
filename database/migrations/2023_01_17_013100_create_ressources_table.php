<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRessourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ressources', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("adresse")->nullable();
            $table->string("code")->nullable()->unique();
            $table->longtext("data_ressource")->nullable();
            $table->longtext("url")->nullable();
            $table->string("description")->nullable();
            $table->integer('format_id')->unsigned();          
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
        Schema::dropIfExists('ressources');
    }
}
