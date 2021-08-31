<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitColumnsToFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->integer('counter_clients')->unsigned()->default(0);
            $table->integer('counter_users')->unsigned()->default(0);
            $table->integer('counter_products')->unsigned()->default(0);
            $table->integer('counter_branches')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->dropColumn('counter_clients');
            $table->dropColumn('counter_users');
            $table->dropColumn('counter_products');
            $table->dropColumn('counter_branches');
        });
    }
}
