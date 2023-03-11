<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChildIsMenstruatorFlag extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('child', function (Blueprint $table) {
			$table->unsignedTinyInteger('is_menstruator')->after('ethnicity')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('child', function (Blueprint $table) {
			//
		});
	}
}
