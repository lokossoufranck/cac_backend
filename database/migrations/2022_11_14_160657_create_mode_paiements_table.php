<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModePaiementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mode_paiements', function (Blueprint $table) {
            $table->id();
            $table->string("libelle");
            $table->boolean('heure_presence')->default(false);
            $table->boolean('prime_anciennete')->default(false);
            $table->boolean('variable_prorata')->default(false);
            $table->boolean('calcul_smic')->default(false);
            $table->boolean('jour_ferie')->default(false);
            $table->integer('mode_calcul_id')->unsigned();
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
        Schema::dropIfExists('mode_paiements');
    }
}
