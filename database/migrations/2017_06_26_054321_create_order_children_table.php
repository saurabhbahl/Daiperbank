<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderChildrenTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('order_child', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned();
			$table->integer('child_id')->unsigned();
			$table->integer('weight')->unsigned()->nullable()->default(null);
			$table->tinyInteger('status_wic')->unsigned()->nullable()->default(null);
			$table->tinyInteger('status_potty_train')->unsigned()->nullable()->default(null);

			$table->unique(['order_id', 'child_id']);

			$table->index('order_id');
			$table->foreign('order_id')
				->references('id')->on('order')
				->onDelete('cascade')
				->onUpdate('cascade');

			$table->index('child_id');
			$table->foreign('child_id')
				->references('id')->on('child')
				->onDelete('restrict')
				->onUpdate('cascade');
		});

		Schema::table('order_item', function($table) {
			$table->foreign('order_child_id')
				->references('id')->on('order_child')
				->onDelete('cascade')
				->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('order_child');
	}
}
