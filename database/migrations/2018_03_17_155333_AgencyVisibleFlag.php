<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgencyVisibleFlag extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('agency', function (Blueprint $table) {
			$table->unsignedTinyInteger('flag_visible')->after('flag_is_admin')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('agency', function (Blueprint $table) {
			//
		});
	}
}
