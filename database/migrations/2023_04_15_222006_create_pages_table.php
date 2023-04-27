<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->longtext("title_1");
            $table->longtext("title_2")->nullable();
            $table->string("code")->nullable()->unique();
            $table->longtext("data_page")->nullable();
            $table->longtext("url")->nullable();
            $table->longtext("description")->nullable();
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
        Schema::dropIfExists('pages');
    }
}
