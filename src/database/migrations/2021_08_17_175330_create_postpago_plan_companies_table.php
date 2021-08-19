<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostpagoPlanCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postpago_plan_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 7, 2);
            $table->integer('num_invoices')->unsigned()->default(0);
            $table->integer('num_clients')->unsigned()->default(0);
            $table->integer('num_products')->unsigned()->default(0);
            $table->integer('num_branches')->unsigned()->default(0);
            $table->integer('num_users')->unsigned()->default(0);
            $table->decimal('prorated_invoice', 5, 2)->default(0);
            $table->decimal('prorated_clients', 5, 2)->default(0);
            $table->decimal('prorated_products', 5, 2)->default(0);
            $table->decimal('prorated_branches', 5, 2)->default(0);
            $table->decimal('prorated_users', 5, 2)->default(0);
            $table->integer('frequency')->unsigned()->default(1);
            $table->boolean('all_sector_docs')->default(true);
            $table->integer('sector_doc_id')->unsigned()->default(0);
            $table->boolean('enable_overflow')->default(false);
            $table->integer('company_id')->unsigned();
            $table->date('start_date');
            $table->decimal('postpago_exceded_amount', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postpago_plan_companies');
    }
}
