<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSousmenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sousmenus', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('menu_id')->unsigned();
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
        //Schema::dropIfExists('sousmenus');
    }
}
