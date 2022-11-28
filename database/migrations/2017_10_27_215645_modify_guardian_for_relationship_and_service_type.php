<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGuardianForRelationshipAndServiceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('child', function (Blueprint $table) {
            $table->string('guardian_relationship', 25)->nullable()->default(null)->after('guardian_id');
        });

        Schema::table('guardian', function (Blueprint $table) {
            $table->string('military_status_new', 35)->nullable()->default(null)->after('military_status');
        });

        DB::transaction(function() {
            DB::statement('UPDATE guardian SET military_status_new = military_status');
            DB::statement('ALTER TABLE guardian DROP COLUMN military_status');
            DB::statement('ALTER TABLE guardian CHANGE COLUMN military_status_new military_status varchar(35) DEFAULT NULL');
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
