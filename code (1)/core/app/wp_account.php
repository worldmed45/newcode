<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class wp_account extends Model
{
    protected $table = 'wp_account';
    protected $fillable = array( 'user_id','eth_balance', 'token_balance');
}
