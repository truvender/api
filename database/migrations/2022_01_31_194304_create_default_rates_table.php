<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('card_id');
            $table->string('country_id');
            $table->string('type_id');
            $table->string('trade_range')->nullable();
            $table->double('buyer_rate');
            $table->double('seller_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('default_rates');
    }
}
