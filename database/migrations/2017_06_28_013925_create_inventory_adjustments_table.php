<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryAdjustmentsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inventory_adjustment', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('created_by_user_id')->unsigned();
			$table->integer('adjustment_type')->unsigned();
			$table->integer('order_id')->unsigned()->nullable()->default(null);
			$table->text('adjustment_note')->nullable()->default(null);
			$table->datetime('adjustment_datetime');
			$table->timestamps();

			$table->index('created_by_user_id');
			$table->index('order_id');

			$table->foreign('created_by_user_id')
				->references('id')->on('users')
				->onDelete('restrict')
				->onUpdate('cascade');

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
	public function down() {
		Schema::dropIfExists('inventory_transaction');
	}
}
