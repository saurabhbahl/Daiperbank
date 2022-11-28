<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFulfillmentOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fulfillment_order', function (Blueprint $table) {
            $table->integer('fulfillment_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->tinyInteger('order_status')->unsigned()->default(1);
            $table->timestamps();

            $table->unique(['fulfillment_id', 'order_id']);

            $table->index('fulfillment_id');
            $table->foreign('fulfillment_id')
                ->references('id')->on('fulfillment')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('order_id');
            $table->foreign('order_id')
                ->references('id')->on('order')
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
        Schema::dropIfExists('fullfilment_order');
    }
}
