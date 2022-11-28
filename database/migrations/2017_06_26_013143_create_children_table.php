<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildrenTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('child', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('agency_id')->unsigned();
			$table->string('uniq_id', 25)->nullable()->default(null);
			$table->string('name', 50)->nullable()->default(null);
			$table->enum('gender', ['m', 'f'])->nullable()->default(null);
			$table->date('dob')->nullable()->default(null);
			$table->integer('guardian_id')->unsigned()->nullable()->default(null);
			$table->enum('guardian_type', ['parent', 'grand-parent', 'aunt-or-uncle', 'adopted-parent', 'other'])->nullable()->default(null);
			$table->string('zip', 10)->nullable()->default(null);
			$table->timestamps();
			$table->softDeletes();

			$table->index('uniq_id');

			$table->index('agency_id');
			$table->foreign('agency_id')
				->references('id')->on('agency')
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
		Schema::dropIfExists('child');
	}
}
