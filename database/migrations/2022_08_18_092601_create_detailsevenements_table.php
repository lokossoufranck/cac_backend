<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsevenementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailsevenements', function (Blueprint $table) {
            $table->id();
            $table->integer('segment_id')->unsigned();
            $table->integer('dysfonctionnement_id')->unsigned();
            $table->integer('evenement_id')->unsigned();  
            $table->double('volume_prevu', 8, 2);
            $table->double('volume_realise', 8, 2); 
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
        Schema::dropIfExists('detailsevenements');
    }
}
