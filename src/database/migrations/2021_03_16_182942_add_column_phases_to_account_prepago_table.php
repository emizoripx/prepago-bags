<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPhasesToAccountPrepagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("alter table account_prepago_bags modify column enabled enum('active','inactive') not null DEFAULT 'active' ");
        \DB::statement("update account_prepago_bags set enabled ='active' where id > 0");
        \DB::statement("alter table account_prepago_bags add column phase enum('Testing','Piloto testing','Production') DEFAULT 'Testing'");

     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_prepago', function (Blueprint $table) {
            $table->dropColumn("phase");
            $table->dropColumn("enabled");
        });
    }
}
