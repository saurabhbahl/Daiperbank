<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('order_item', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned();
			$table->integer('order_child_id')->unsigned()->nullable()->default(null);
			$table->integer('product_id')->unsigned();
			$table->integer('quantity')->unsigned();
			$table->tinyInteger('flag_approved')->unsigned()->default(0);
			$table->softDeletes();

			$table->unique(['order_id', 'order_child_id', 'product_id']);

			$table->foreign('order_id')
				->references('id')->on('order')
				->onDelete('cascade')
				->onUpdate('cascade');

			$table->foreign('product_id')
				->references('id')->on('product')
				->onDelete('restrict')
				->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('order_item');
	}
}
