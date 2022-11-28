<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MaxAgencyIdExtension extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('agency', function (Blueprint $table) {
			DB::statement("ALTER TABLE `agency` CHANGE COLUMN `id_prefix` `id_prefix` varchar(15) DEFAULT 'NULL'");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
	}
}
