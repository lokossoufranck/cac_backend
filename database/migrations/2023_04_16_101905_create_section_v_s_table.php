<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionVSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_v_s', function (Blueprint $table) {
            $table->id();
            $table->longtext("title_1");
            $table->longtext("title_2")->nullable();
            $table->string("code")->nullable()->unique();
            $table->longtext("data_sectionv")->nullable();
            $table->longtext("url")->nullable();
            $table->longtext("description_1")->nullable();
            $table->longtext("description_2")->nullable();
            $table->integer('format_id')->unsigned(); 
            $table->integer('page_id')->unsigned();   
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
        Schema::dropIfExists('section_v_s');
    }
}
