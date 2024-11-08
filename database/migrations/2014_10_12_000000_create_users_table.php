<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('agency_id')->unsigned()->nullable()->default(null);
			$table->string('name', 100);
			$table->string('email', 100)->unique();
			$table->string('username', 25)->unique();
			$table->string('password');
			$table->rememberToken();
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('users');
	}
}
