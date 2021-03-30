<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepagoBagsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepago_bags_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('prepago_bag_id');
            $table->unsignedInteger('company_id');
            $table->decimal('amount',20,5);
            $table->datetime('paid_on')->nullable();
            $table->string('status_code')->default("-1");
            $table->json('extras')->nullable();
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
        Schema::dropIfExists('prepago_bags_payments');
    }
}
