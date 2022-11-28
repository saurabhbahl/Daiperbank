<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('order', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('agency_id')->unsigned();
			$table->enum('order_type', ['itemized', 'bulk'])->default('itemized');
			$table->enum('order_status', ['draft', 'pending_approval', 'pending_pickup', 'fulfilled', 'cancelled', 'rejected'])->default('draft');
			$table->integer('cloned_from_order_id')->unsigned()->nullable()->default(null);
			$table->integer('pickup_date_id')->unsigned();
			$table->integer('created_by_user_id')->unsigned();
			$table->timestamps();

			$table->index('pickup_date_id');
			$table->index('order_status');

			$table->index('agency_id');
			$table->foreign('agency_id')
				->references('id')->on('agency')
				->onDelete('restrict')
				->onUpdate('cascade');

			$table->index('cloned_from_order_id');
			$table->foreign('cloned_from_order_id')
				->references('id')->on('order')
				->onDelete('restrict')
				->onUpdate('cascade');

			$table->index('created_by_user_id');
			$table->foreign('created_by_user_id')
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
		Schema::dropIfExists('order');
	}
}
