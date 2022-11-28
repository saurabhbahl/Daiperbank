<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyContactRelationTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('agency_contact', function (Blueprint $table) {
			$table->integer('agency_id')->unsigned();
			$table->integer('contact_id')->unsigned();

			$table->primary(['agency_id', 'contact_id']);

			$table->foreign('agency_id')
				->references('id')
				->on('agency')
				->onDelete('cascade')
				->onUpdate('cascade');

			$table->foreign('contact_id')
				->references('id')
				->on('contact')
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
		Schema::dropIfExists('agency_contact');
	}
}
