<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class withdrawal_request extends Model
{
    protected $table = 'withdrawal_request';
    protected $fillable = array( 'user_id','amount_send', 'fee','total','status','address','hash','item');
}
