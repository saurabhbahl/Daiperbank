<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_date', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('pickup_date');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('order', function($table) {
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
        Schema::dropIfExists('pickup_date');
    }
}
