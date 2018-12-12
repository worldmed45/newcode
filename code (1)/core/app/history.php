<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    protected $table = 'history';
    protected $fillable = array( 'user_id','task', 'item', 'amount','amount_by','hash','status');
}
