<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsOfFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company', function (Blueprint $table) {
            $table->dropColumn('invoice_number_available');
            $table->dropColumn('acumulative');
            $table->dropColumn('duedate');
            $table->dropColumn('invoice_counter');
            $table->dropColumn('sector_document_type_code');
            $table->dropColumn('delete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
