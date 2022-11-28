<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFulfillmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fulfillment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_date_id')->unsigned();
            $table->timestamps();

            $table->index('pickup_date_id');
            $table->foreign('pickup_date_id')
                    ->references('id')->on('pickup_date')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fulfillment');
    }
}
