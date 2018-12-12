<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class eth_deposit extends Model
{
    protected $table = 'eth_deposit';
    protected $fillable = array( 'user_id','hash', 'status', 'amount');
}
