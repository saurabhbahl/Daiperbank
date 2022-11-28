<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuardiansTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('guardian', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('agency_id')->unsigned();
			$table->string('name', 50);
			$table->enum('gender', ['m', 'f']);
			$table->date('dob');
			$table->string('phone', 20)->nullable()->default(null);
			$table->string('email', 100)->nullable()->default(null);
			$table->string('address', 50)->nullable()->default(null);
			$table->string('address_2', 50)->nullable()->default(null);
			$table->string('city', 35)->nullable()->default(null);
			$table->string('state', 2)->nullable()->default(null);
			$table->string('zip', 10)->nullable()->default(null);
			$table->enum('military_status', ['non-military', 'active', 'retired', 'reserve', 'guard', 'other']);
			$table->timestamps();
		});

		Schema::table('child', function($table) {
			$table->index('guardian_id');
			$table->foreign('guardian_id')
				->references('id')->on('guardian')
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
		Schema::dropIfExists('guardians');
	}
}
