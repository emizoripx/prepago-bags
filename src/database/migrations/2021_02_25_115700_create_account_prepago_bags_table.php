<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPrepagoBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_prepago_bags', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->integer('invoice_number_available')->nullable()->default(0);
            $table->boolean('acumulative')->nullable();
            $table->dateTime('duedate')->nullable();

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
        Schema::dropIfExists('account_prepago_bags');
    }
}
