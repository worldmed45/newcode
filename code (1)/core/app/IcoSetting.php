<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IcoSetting extends Model
{
    protected $table = 'ico_setting';
    protected $fillable = array( 'Hard_cap','Soft_cap', 'Start_time', 'End_time','Token_sold','ETH_raised','TokenPerETH','referral_bonus','	IcoSaleText','eth_fees','ETH_merchant_address','ETH_merchant_private');
}
