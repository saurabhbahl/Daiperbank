<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildDatasTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('child_data', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('child_id')->unsigned();
			$table->integer('updated_by_user_id')->unsigned();
			$table->integer('weight');
			$table->tinyInteger('status_wic');
			$table->tinyInteger('status_potty_train');
			$table->timestamps();

			$table->index('child_id');
			$table->foreign('child_id')
				->references('id')->on('child')
				->onDelete('cascade')
				->onUpdate('cascade');

			$table->index('updated_by_user_id');
			$table->foreign('updated_by_user_id')
				->references('id')->on('users')
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
		Schema::dropIfExists('child_data');
	}
}
