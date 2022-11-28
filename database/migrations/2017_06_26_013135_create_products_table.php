<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('product', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('product_category_id')->unsigned();
			$table->string('name', 50);
			$table->timestamps();

			$table->index('product_category_id');

			$table->foreign('product_category_id')
				->references('id')->on('product_category')
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
		Schema::dropIfExists('product');
	}
}
