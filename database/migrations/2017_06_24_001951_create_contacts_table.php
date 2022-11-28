<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('contact', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('phone', 10)->nullable()->default(null);
			$table->string('phone_extension', 10)->nullable()->default(null);;
			$table->string('email', 100)->nullable()->default(null);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('contact');
	}
}
