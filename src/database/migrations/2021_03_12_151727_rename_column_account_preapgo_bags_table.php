<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnAccountPreapgoBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_prepago_bags', function (Blueprint $table) {
            $table->renameColumn('account_id', 'company_id');
            $table->boolean('is_postpago')->nullable()->default(false);
            $table->unsignedInteger('invoice_counter')->default(0);
            $table->boolean('enabled')->nullable();
        });

        Schema::table('prepago_bags_purchase_historial', function (Blueprint $table) {
            $table->renameColumn('account_id', 'company_id');
        });

        Schema::table('fel_invoice_requests', function (Blueprint $table) {
            $table->dropColumn('account_id');
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
