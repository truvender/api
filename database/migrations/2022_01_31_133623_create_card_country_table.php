<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_country', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('card_id');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->string('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->boolean('status')->default(true)->comment('1 = available, 0 = not available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_country');
    }
}
