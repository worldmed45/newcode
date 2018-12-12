<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcoSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ico_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Hard_cap');
            $table->string('Soft_cap')->nullable();
            $table->string('Start_time')->nullable();
            $table->string('End_time')->nullable();
            $table->string('Token_sold')->nullable();
            $table->integer('ETH_raised')->nullable();
            $table->integer('TokenPerETH')->nullable();
            $table->integer('referral_bonus')->nullable();
            $table->integer('IcoSaleText')->nullable();
            $table->integer('eth_fees')->nullable();
            $table->integer('ETH_merchant_address')->nullable();
            $table->string('ETH_merchant_private')->nullable();
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
        Schema::dropIfExists('ico_setting');
    }
}
