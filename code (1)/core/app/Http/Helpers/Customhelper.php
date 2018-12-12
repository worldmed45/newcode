<?php
namespace App\Helpers;
use DB;
use Hash;
use Auth;
use Input;
use Redirect;
use Session;
use Validator;

class Customhelper {
    public static function usersdetail() {
       $users=DB::table('users')->get();
       return $users;
    }
}