<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelPieceIdentitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personnel_piece_identites', function (Blueprint $table) {
            $table->id();
            $table->integer('personnel_id')->unsigned();
            $table->integer('piece_identite_categorie_id')->unsigned();
            $table->string("numero");
            $table->date("date_expiration");
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
        Schema::dropIfExists('personnel_piece_identites');
    }
}
