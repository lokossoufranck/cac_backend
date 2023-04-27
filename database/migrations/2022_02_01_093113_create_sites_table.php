<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

 /*   public function up()
     {
         Schema::table('sites', function (Blueprint $table) {
             $table->string('adresse')->after('nom')->nullable();
             $table->string('sigle')->after('adresse')->nullable();
         });
     }*/
    public function up()
    {

        
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('adresse');
            $table->string('sigle');
            $table->string('url_logo')->nullable();
            $table->string('url_header_1')->nullable();
            $table->string('url_header_2')->nullable();
            $table->string('url_footer_1')->nullable();
            $table->string('url_footer_2')->nullable();
            $table->string('tel1')->nullable();
            $table->string('tel2')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->boolean('is_siege')->default(false);
            $table->boolean('actif')->default(true);
            $table->integer('pays_id')->unsigned();
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
      //  Schema::dropIfExists('sites');
    }
}
