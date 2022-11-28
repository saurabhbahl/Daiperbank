<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inventory', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('inventory_adjustment_id')->unsigned();
			$table->enum('txn_type', ['credit', 'debit']);
			$table->integer('product_id')->unsigned();
			$table->integer('amount')->unsigned();
			$table->timestamps();

			$table->index('product_id');
			$table->index('inventory_adjustment_id');

			$table->foreign('product_id')
				->references('id')->on('product')
				->onDelete('restrict')
				->onUpdate('cascade');

			$table->foreign('inventory_adjustment_id')
				->references('id')->on('inventory_adjustment')
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
		Schema::dropIfExists('inventory');
	}
}
