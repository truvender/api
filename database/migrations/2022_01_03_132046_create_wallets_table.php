<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id');
            $table->string('type')->comment("crypto or fiat");
            $table->double('balance')->default(0);
            $table->string('asset_id')->nullable()->comment("if type is crypto");
            $table->string('address')->nullable()->comment("crypto wallet address");
            $table->string('private')->nullable()->comment("crypto wallet key");
            $table->string('public')->nullable()->comment("crypto wallet key");
            $table->string('label')->nullable();
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
        Schema::dropIfExists('wallets');
    }
}
