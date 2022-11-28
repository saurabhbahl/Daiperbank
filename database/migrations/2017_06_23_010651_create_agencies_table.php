<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgenciesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('agency', function (Blueprint $table) {
			$table->increments('id');
			$table->string('id_prefix', 10)->nullable()->default(null)->unique();
			$table->string('name', 100);
			$table->string('address', 35);
			$table->string('address_2', 35)->nullable()->default(null);
			$table->string('city', 35);
			$table->string('state', 2);
			$table->string('zip', 10);
			$table->enum('agency_status', ['active', 'inactive'])->default('active');
			$table->tinyInteger('flag_can_bulk_order')->unsigned()->default(0);
			$table->tinyInteger('flag_is_admin')->unsigned()->default(0);
			$table->string('agency_timezone', 75)->default('America/New_York');
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('agency');
	}
}
