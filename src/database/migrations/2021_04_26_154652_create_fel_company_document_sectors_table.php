<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelCompanyDocumentSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_company_document_sectors', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_number_available')->default(0);
            $table->boolean('accumulative')->default(false);
            $table->date('duedate')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedBigInteger('fel_company_id');
            $table->unsignedInteger('fel_doc_sector_id');
            $table->integer('counter')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fel_company_id')->references('id')->on('fel_company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fel_company_document_sectors');
    }
}
