<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fel_pines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prepago_bag_id');
            $table->integer('lote');
            $table->string('pin')->unique();
            $table->datetime('due_date_pin')->nullable();
            $table->datetime('usage_date')->nullable();
            $table->timestamps();

            $table->foreign('prepago_bag_id')->references('id')->on('prepago_bags');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fel_pins');
    }
}
