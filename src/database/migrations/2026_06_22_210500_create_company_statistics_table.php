<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->integer('total_clients')->default(0);
            $table->integer('total_products')->default(0);
            $table->integer('total_branches')->default(0);
            $table->integer('total_users')->default(0);
            $table->decimal('total_due_invoices_amount', 15, 2)->default(0);
            $table->integer('total_due_invoices_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_statistics');
    }
}
