<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postpago_plan_companies', function (Blueprint $table) {
            $table->boolean('due_invoice_suspend')->default(0); //inactive
            $table->unsignedInteger('due_invoice_limit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postpago_plan_companies', function (Blueprint $table) {
            $table->dropColumn('due_invoice_suspend');
            $table->dropColumn('due_invoice_limit');
        });
    }
}
