<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepagoBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepago_bags', function (Blueprint $table) {
            $table->id();
            $table->integer('number_invoices')->nullable();
            $table->string('name', 250)->nullable();
            $table->enum('frequency', ['monthly', 'yearly'])->nullable();
            $table->boolean('acumulative')->nullable();
            
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
        Schema::dropIfExists('prepago_bags');
    }
}
