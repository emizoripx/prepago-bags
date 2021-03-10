<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPrepagoBagPurchaseHistorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prepago_bags_purchase_historial', function (Blueprint $table) {
            $table->unsignedBigInteger('bag_id')->nullable();

            $table->foreign('bag_id')->references('id')->on('prepago_bags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prepago_bags_purchase_historial', function (Blueprint $table) {
            $table->dropForeign('bag_id');
            $table->dropColumn('bag_id');
        });
    }
}
