<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string("nom");
            $table->string("prenoms");
            $table->string("matricule");
            $table->string("nb_enfants_a_charge");
            $table->string("numero_cnss");
            $table->string("email");
            $table->string("sexe");
            $table->date("date_naissance");
            $table->date("date_entree");
            $table->string("adresse");
            $table->string("situation_matrimoniale");
            $table->string("telephone");
            $table->string("domaine_etude");
            $table->integer('nationalite_id')->unsigned();
            $table->integer('etude_niveau_id')->unsigned();
            $table->string('url_photo_identite')->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('service_id')->unsigned();
            $table->string("personne_a_contacter");
            $table->integer('status_id')->unsigned();
            $table->integer('fonction_id')->unsigned();
            $table->integer('permis_categorie_id')->unsigned();
            $table->boolean('domiciliation_irrevocable')->default(true);
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
        Schema::dropIfExists('personnels');
    }
}
