<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectorDocumentTypeColumnToAccountPrepagoBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_prepago_bags', function (Blueprint $table) {
            $table->unsignedInteger('sector_document_type_code')->default(1);
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
            $table->dropColumn('sector_document_type_code');
        });
    }
}
