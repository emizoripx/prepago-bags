<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExportacionAccountPrepagoBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_prepago_bags', function (Blueprint $table) {
            $table->string('ruex')->nullable();
            $table->string('nim')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_prepago_bags', function (Blueprint $table) {
            $table->dropColumn('ruex');
            $table->dropColumn('nim');
        });
    }
}
