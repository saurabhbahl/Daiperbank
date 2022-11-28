<?php

use Illuminate\Database\Migrations\Migration;

class RemoveUserEmailUniqueIndex extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		DB::transaction(function () {
			DB::statement('ALTER TABLE `users` DROP INDEX IF EXISTS `users_email_unique`');
			DB::statement('ALTER TABLE `users` ADD INDEX `users_email_idx` (`email`) comment ""');
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
