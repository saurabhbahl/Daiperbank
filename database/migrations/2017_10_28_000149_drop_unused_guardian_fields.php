<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnusedGuardianFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function() {
            DB::statement('ALTER TABLE guardian DROP COLUMN gender');
            DB::statement('ALTER TABLE guardian DROP COLUMN dob');
            DB::statement('ALTER TABLE guardian DROP COLUMN phone');
            DB::statement('ALTER TABLE guardian DROP COLUMN email');
            DB::statement('ALTER TABLE guardian DROP COLUMN address');
            DB::statement('ALTER TABLE guardian DROP COLUMN address_2');
            DB::statement('ALTER TABLE guardian DROP COLUMN city');
            DB::statement('ALTER TABLE guardian DROP COLUMN state');
            DB::statement('ALTER TABLE guardian DROP COLUMN zip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
