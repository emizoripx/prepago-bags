<?php

use Illuminate\Database\Migrations\Migration;

class AddFullSearchIndexFelCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       \DB::statement(\DB::raw('CREATE FULLTEXT INDEX settings_fulltext_search ON companies(settings);'));
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
