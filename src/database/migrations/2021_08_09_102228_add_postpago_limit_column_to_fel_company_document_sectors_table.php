<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostpagoLimitColumnToFelCompanyDocumentSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fel_company_document_sectors', function (Blueprint $table) {
            $table->integer('postpago_limit')->default(0)->after('postpago_counter');
            $table->integer('postpago_exceded_limit')->default(0)->after('postpago_limit');
            $table->date('start_date')->nullable()->after('postpago_exceded_limit');
            $table->integer('frequency')->default(0)->after('start_date');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fel_company_document_sectors', function (Blueprint $table) {
            $table->dropColumn('postpago_limit');
            $table->dropColumn('postpago_exceded_limit');
            $table->dropColumn('start_date');
            $table->dropColumn('frequency');

        });
    }
}
