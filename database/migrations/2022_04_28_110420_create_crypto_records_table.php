<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transction_id');
            $table->string('asset_id');
            $table->longText('tx_hash')->nullable();
            $table->string('block_height')->nullable();
            $table->string('tx_input_n')->nullable();
            $table->string('tx_output_n')->nullable();
            $table->string('value')->nullable();
            $table->string('ref_balance')->nullable();
            $table->boolean('double_spend')->default(false);
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
        Schema::dropIfExists('crypto_records');
    }
}
