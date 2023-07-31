<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddiotionalResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        Schema::create('additionalres', function (Blueprint $table) {
=======
        Schema::create('additionalrs', function (Blueprint $table) {
>>>>>>> Development
=======
        Schema::create('additionalrs', function (Blueprint $table) {
>>>>>>> Development
            $table->increments('id');
            $table->string('file');
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
        Schema::dropIfExists('additionalresources');
    }
}
