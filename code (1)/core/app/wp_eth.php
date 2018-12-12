<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class wp_eth extends Model
{
    protected $table = 'wp_eth';
    protected $fillable = array( 'user_id','eth_address', 'private_key');
}
