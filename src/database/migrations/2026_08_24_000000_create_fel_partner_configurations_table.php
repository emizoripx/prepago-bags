<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFelPartnerConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("fel_partner_configurations", function (Blueprint $table) {
            $table->id();
            $table->string("environment")->unique(); // "testing" or "production"
            $table->string("api_url")->nullable();
            $table->text("partner_token")->nullable();
            $table->string("webhook_url")->nullable();
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
        Schema::dropIfExists("fel_partner_configurations");
    }
}
