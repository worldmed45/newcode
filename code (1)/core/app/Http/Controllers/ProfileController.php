<?php

namespace App\Http\Controllers;

use App\Gateway;
use App\General;
use App\Ico;
use App\Lib\GoogleAuthenticator;
use App\Sell;
use App\User;
use App\wp_account;
use App\wp_eth;
use App\history;
use App\eth_deposit;
use Auth;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as input;
use Session;
use App\IcoSetting;
class ProfileController extends Controller
{
	public function __construct()
    {
        $this->middleware(['auth','ckstatus']);
    }
    public function index()
    {
      
    $user_ID = Auth::user()->id; 
      $address = wp_eth::where('user_id', $user_ID)->get();

      $ico_setting = IcoSetting::first();
      $ETH_merchant_address = $ico_setting->ETH_merchant_address;
      $ETH_merchant_private = $ico_setting->ETH_merchant_private;
      $BTC_merchant_address = $ico_setting->BTC_merchant_address;
      $LTC_merchant_address = $ico_setting->LTC_merchant_address;
      $BTC_merchant_api_key = $ico_setting->BTC_merchant_api_key;
      $LTC_merchant_api_key = $ico_setting->LTC_merchant_api_key;
      $BTC_merchant_secret_pin = $ico_setting->BTC_merchant_secret_pin;
      $LTC_merchant_secret_pin = $ico_setting->LTC_merchant_secret_pin;
          

     

      if(sizeof($address)){

       //FOR  fetch All address  from the database
         
        $user_btc_address=$address[0]->btc_address;
        $user_ltc_address=$address[0]->ltc_address;
        $user_eth_address=$address[0]->eth_address;
        $user_eth_private_key=$address[0]->private_key;

       
        //FOR BTC fetch btc balance from btc address
        $btc_merchant_apikey= $BTC_merchant_api_key;
        $btc_url='https://block.io/api/v2/get_address_balance/?api_key='.$btc_merchant_apikey.'&addresses='.$user_btc_address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $btc_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $btc_result = curl_exec($ch);
        if(curl_errno($ch)) {
         error_log('cURL error when connecting to ' . $btc_url . ': ' . curl_error($ch));
        }
        curl_close($ch);
        $btc_user_address=json_decode($btc_result,true);
        $btc_avail_balance=$btc_user_address['data']['available_balance'];



         //FOR ltc fetch ltc balance from ltc address

        $ltc_merchant_apikey= $LTC_merchant_api_key;
        $ltc_url='https://block.io/api/v2/get_address_balance/?api_key='.$ltc_merchant_apikey.'&addresses='.$user_ltc_address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ltc_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ltc_result = curl_exec($ch);
        if(curl_errno($ch)) {
         error_log('cURL error when connecting to ' . $ltc_url . ': ' . curl_error($ch));
        }
        curl_close($ch);
        $ltc_user_address=json_decode($ltc_result,true);
        $ltc_avail_balance=$ltc_user_address['data']['available_balance'];

        //FOR eth fetch eth balance from eth address

        $eth_url = "https://world-meds.herokuapp.com/?task=getEther&ToAddress=".$user_eth_address;
        $cSession = curl_init(); 
        curl_setopt($cSession,CURLOPT_URL,$eth_url);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cSession,CURLOPT_HEADER, false); 
        $Ether=curl_exec($cSession);
        curl_close($cSession); 
        $balance =     wp_account::where('user_id', $user_ID)->get();
         if (sizeof($balance)<1) {
           DB::insert('insert into wp_account (user_id, eth_balance, token_balance,btc_balance,ltc_balance) values (?, ?,?,?,?)', [$user_ID, 0,0,0,0]);
         }else{
          
           }

       
          
         if($btc_avail_balance > 0){

          $btc_merchant_apikey= $BTC_merchant_api_key;
          $merchant_pin = $BTC_merchant_secret_pin;
          $merchant_address = $BTC_merchant_address;
          $curl = curl_init();
          $url1='https://block.io/api/v2/withdraw_from_addresses/?api_key='.$btc_merchant_apikey.'&from_addresses='.$user_btc_address.'&to_addresses='.$merchant_address.'&amounts='.$btc_avail_balance.'&pin='.$merchant_pin;
          curl_setopt_array($curl, array(
          CURLOPT_URL => $url1,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "",
         ));

          $response = curl_exec($curl);
          $check_max_withdrawal = json_decode($response,true);
          $status = $check_max_withdrawal['status'];
          $max_withdrawal = $check_max_withdrawal['data']['max_withdrawal_available'];
          if($status=='fail'){
              $btc_merchant_apikey= $BTC_merchant_api_key;
              $merchant_pin = $BTC_merchant_secret_pin;
              $merchant_address = $BTC_merchant_address;
              $curl = curl_init(); 
              $url2='https://block.io/api/v2/withdraw_from_addresses/?api_key='.$btc_merchant_apikey.'&from_addresses='.$user_btc_address.'&to_addresses='.$merchant_address.'&amounts='.$max_withdrawal.'&pin='.$merchant_pin;
             
              curl_setopt_array($curl, array(
              CURLOPT_URL => $url2,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
         ));

         $response1 = curl_exec($curl);
         $check_max_withdrawal1 = json_decode($response1,true); 
         $status1 = $check_max_withdrawal1['status'];
         $tx_id = $check_max_withdrawal1['data']['txid'];
         $amount_sent = $check_max_withdrawal1['data']['amount_sent'];
         $network = $check_max_withdrawal1['data']['network'];
         DB::insert('insert into eth_deposit (user_id, amount, hash,status,coin_name) values (?, ?,?,?,?)', [$user_ID, $amount_sent,$tx_id,'','BTC']);
        }else{
            
        }  
        }else{
          
        }

        if( $ltc_avail_balance > 0){
          $ltc_merchant_apikey= $LTC_merchant_api_key;
          $merchant_pin = $LTC_merchant_secret_pin;
          $merchant_address = $LTC_merchant_address;
          $curl = curl_init();
          $url1='https://block.io/api/v2/withdraw_from_addresses/?api_key='.$ltc_merchant_apikey.'&from_addresses='.$user_ltc_address.'&to_addresses='.$merchant_address.'&amounts='.$ltc_avail_balance.'&pin='.$merchant_pin;
          curl_setopt_array($curl, array(
          CURLOPT_URL => $url1,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "",
         ));

          $response = curl_exec($curl);
          $check_max_withdrawal = json_decode($response,true);
          $status = $check_max_withdrawal['status'];
          $max_withdrawal = $check_max_withdrawal['data']['max_withdrawal_available'];
           if($status=='fail'){
               $ltc_merchant_apikey= $LTC_merchant_api_key;
               $merchant_pin = $LTC_merchant_secret_pin;
               $merchant_address = $LTC_merchant_address;
               $curl = curl_init(); 
               $url2='https://block.io/api/v2/withdraw_from_addresses/?api_key='.$ltc_merchant_apikey.'&from_addresses='.$user_ltc_address.'&to_addresses='.$merchant_address.'&amounts='.$max_withdrawal.'&pin='.$merchant_pin;
             
              curl_setopt_array($curl, array(
              CURLOPT_URL => $url2,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
         ));

         $response1 = curl_exec($curl);
         $check_max_withdrawal1 = json_decode($response1,true); 
         $status1 = $check_max_withdrawal1['status'];
         $tx_id = $check_max_withdrawal1['data']['txid'];
         $amount_sent = $check_max_withdrawal1['data']['amount_sent'];
         $network = $check_max_withdrawal1['data']['network'];
         DB::insert('insert into eth_deposit (user_id, amount, hash,status,coin_name) values (?, ?,?,?,?)', [$user_ID, $amount_sent,$tx_id,'','LTC']);
        }else{
            
        }



        }else{
          
        }

        if ($Ether > 210000000000000) {
           $ico_setting = IcoSetting::first();
           $ETH_Merchant_Address = $ico_setting->ETH_merchant_address;
           $url = "https://world-meds.herokuapp.com/?task=AllEtherTransfer&ToAddress=".$ETH_Merchant_Address."&FromAddress=".$eth[0]->eth_address."&PrivateKey=".$eth[0]->private_key;
           $cSession = curl_init();
           curl_setopt($cSession,CURLOPT_URL,$url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false); 
           $Hash=curl_exec($cSession);
           curl_close($cSession);
           $Hash = str_replace('"','',$Hash,$count);
           if ($count==2) {
             DB::insert('insert into eth_deposit (user_id, amount, hash,status,coin_name) values (?, ?,?,?,?)', [$user_ID, $Ether,$Hash,'','ETH']);
           }
        }else{
          
        }


        $eth_pending = eth_deposit::where('user_id', $user_ID)->where('coin_name','ETH')->get();
        $btc_pending = eth_deposit::where('user_id', $user_ID)->where('coin_name','BTC')->get();
        $ltc_pending = eth_deposit::where('user_id', $user_ID)->where('coin_name','LTC')->get();

       // btc hash confirm

       foreach ($btc_pending as $key) {
        if ($key->status == "") {
          $curl = curl_init(); 
          $btc_merchant_apikey= $BTC_merchant_api_key;
          $url2='https://block.io/api/v2/get_raw_transaction/?api_key='.$btc_merchant_apikey.'&txid='.$key->hash;
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url2,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
         ));

         $status = curl_exec($curl);
         $response = json_decode($status,true);
         $statuscheck = $response['status'];

         if($statuscheck=="success"){
            $updte= eth_deposit::where('id', $key->id)->update(['status' => $statuscheck]);
                        $sql_balance =  wp_account::where('user_id', $user_ID)->get();
                        $btc_balance = $sql_balance[0]->btc_balance;
                        $update_balence = $btc_balance + ($key->amount);
                        $amount_btc=$key->amount;
                        $hash=$key->hash;
                        $updtes = wp_account::where('user_id', $user_ID)->update(['btc_balance' => $update_balence]);
                        DB::insert('insert into history (user_id, task, item,amount,hash,status,amount_by) values (?, ?,?,?,?,?,?)', [$user_ID, 'Deposit','BTC',$amount_btc,$hash,'Success',0]);
         }else{
          
         $updtenot= eth_deposit::where('id', $key->id)->update(['status' => 'Not']);
                            
                        
         }
        }
       }

       foreach ($ltc_pending as $key) {
        if ($key->status == "") {
          $curl = curl_init(); 
          $ltc_merchant_apikey= $LTC_merchant_api_key;
          $url2='https://block.io/api/v2/get_raw_transaction/?api_key='.$btc_merchant_apikey.'&txid='.$key->hash;
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url2,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
         ));

         $status = curl_exec($curl);
         $response = json_decode($status,true);
         $statuscheck = $response['status'];

         if($statuscheck=="success"){
            $updte= eth_deposit::where('id', $key->id)->update(['status' => $statuscheck]);
                        $sql_balance =  wp_account::where('user_id', $user_ID)->get();
                        $ltc_balance = $sql_balance[0]->ltc_balance;
                        $update_balence = $ltc_balance + ($key->amount);
                        $amount_ltc=$key->amount;
                        $hash=$key->hash;
                        $updtes = wp_account::where('user_id', $user_ID)->update(['ltc_balance' => $update_balence]);
                        DB::insert('insert into history (user_id, task, item,amount,hash,status,amount_by) values (?, ?,?,?,?,?,?)', [$user_ID, 'Deposit','LTC',$amount_ltc,$hash,'Success',0]);
         }else{
          
         $updtenot= eth_deposit::where('id', $key->id)->update(['status' => 'Not']);
                            
                        
         }
        }
       }


       foreach ($eth_pending as $key) {


          if ($key->status == "") {
            $url = "https://world-meds.herokuapp.com/?task=confirm&hash=".$key->hash;
            $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL,$url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $status=curl_exec($cSession);
                curl_close($cSession);
                $status = str_replace('"','',$status,$count);
                  if ($count==2) {
                    if ($status=="Done") {
                       $updte= eth_deposit::where('id', $key->id)->update(['status' => $status]);
                        $sql_balance =  wp_account::where('user_id', $user_ID)->get();
                        $eth_balance = $sql_balance[0]->eth_balance;
                        $update_balence = $eth_balance + ($key->amount/1000000000000000000);
                        $amount_eth=$key->amount/1000000000000000000;
                        $hash=$key->hash;
                        $updtes = wp_account::where('user_id', $user_ID)->update(['eth_balance' => $update_balence]);
                        DB::insert('insert into history (user_id, task, item,amount,hash,status,amount_by) values (?, ?,?,?,?,?,?)', [$user_ID, 'Deposit','ETH',$amount_eth,$hash,'Success',0]);
                    }else{
                       if ($status=="Not") {
                            $updtenot= eth_deposit::where('id', $key->id)->update(['status' => $status]);
                            
                        }
                    }
                }
          }
        }

        }else{
        
        //FOR BTC(GENERATE NEW ADDRESS FOR NEW USER)

        $btc_merchant_apikey= $BTC_merchant_api_key;
        $btc_url='https://block.io/api/v2/get_new_address/?api_key='.$btc_merchant_apikey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $btc_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $btc_result = curl_exec($ch);
        if(curl_errno($ch)) {
        error_log('cURL error when connecting to ' . $btc_url . ': ' . curl_error($ch));
        }
        curl_close($ch);
        $btc_user_address=json_decode($btc_result,true);
        $btc_address=$btc_user_address['data']['address'];

        //FOR LTC(GENERATE NEW ADDRESS FOR NEW USER)

        $ltc_merchant_apikey= $LTC_merchant_api_key;
        $ltc_url='https://block.io/api/v2/get_new_address/?api_key='.$ltc_merchant_apikey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ltc_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ltc_result = curl_exec($ch);
        if(curl_errno($ch)) {
        error_log('cURL error when connecting to ' . $ltc_url . ': ' . curl_error($ch));
        }
        curl_close($ch);
        $ltc_user_address=json_decode($ltc_result,true);
        $ltc_address=$ltc_user_address['data']['address'];

       
        
        // FOR ETH(GENERATE NEW ADDRESS FOR NEW USER)
        
        $cSession = curl_init(); 
        curl_setopt($cSession,CURLOPT_URL,"https://world-meds.herokuapp.com/?task=Create");
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cSession,CURLOPT_HEADER, false); 
        $eth_result=curl_exec($cSession);
        curl_close($cSession);
        $a = json_decode($eth_result,true);
        $a['privateKey']=str_replace("0x","",$a['privateKey'],$i);

        if ($i==1) {
          DB::insert('insert into wp_eth (user_id, eth_address,private_key,btc_address,btc_private_key,ltc_address,ltc_private_key) values (?, ?,?,?,?,?,?)', [$user_ID, $a['address'],$a['privateKey'],$btc_address,0,$ltc_address,0]);
          DB::insert('insert into wp_account (user_id, eth_balance, token_balance,btc_balance,ltc_balance) values (?, ?,?,?,?)', [$user_ID, 0,0,0,0]);
        }
        }

       $eth_status_pending = history::where('user_id', $user_ID)->where('item','ETH')->get();
       if (sizeof($eth_status_pending)) {
         foreach ($eth_status_pending as $status_check) {
           if ($status_check->status !="Done" && $status_check->status !="Fail" ) {
              $url = "https://world-meds.herokuapp.com/?task=confirm&hash=".$status_check->hash;
                    $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $status=curl_exec($cSession);
                    curl_close($cSession);
                    $status = str_replace('"','',$status,$count);
                      if ($status=="Done") {
                             $updte= history::where('id', $status_check->id)->update(['status' => $status]);
                              
                       }else{
                            if ($status=="Not") {
                                
                                $updte= history::where('id', $status_check->id)->update(['status' => 'Fail']);
                            }
                        }

           }
         }
       }else{
         
       }

        $btc_status_pending = history::where('user_id', $user_ID)->where('item','BTC')->get();
        if (sizeof($btc_status_pending)) {
          foreach ($btc_status_pending as $status_check) {
            if ($status_check->status !="Success" && $status_check->status !="Fail" ) {
               $curl = curl_init(); 
               $btc_merchant_apikey= $BTC_merchant_api_key;
               $url2='https://block.io/api/v2/get_raw_transaction/?api_key='.$btc_merchant_apikey.'&txid='.$status_check->hash;
               curl_setopt_array($curl, array(
              CURLOPT_URL => $url2,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
         ));
               $status = curl_exec($curl);
               $response = json_decode($status,true);
               $statuscheck = $response['status'];
                      if ($statuscheck=="success") {
                             $updte= history::where('id', $status_check->id)->update(['status' => $statuscheck]);
                              
                       }else{
                            if ($statuscheck=="Not") {
                                
                                $updte= history::where('id', $status_check->id)->update(['status' => 'Fail']);
                            }
                        }

           }
          }
        }else{
          
        }
        $ltc_status_pending = history::where('user_id', $user_ID)->where('item','LTC')->get();
         if (sizeof($ltc_status_pending)) {
          foreach ($ltc_status_pending as $status_check) {
            if ($status_check->status !="Success" && $status_check->status !="Fail" ) {
               $curl = curl_init(); 
               $ltc_merchant_apikey= $LTC_merchant_api_key;
              $url2='https://block.io/api/v2/get_raw_transaction/?api_key='.$btc_merchant_apikey.'&txid='.$key->hash;
              curl_setopt_array($curl, array(
              CURLOPT_URL => $url2,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
         ));

         $status = curl_exec($curl);
         $response = json_decode($status,true);
         $statuscheck = $response['status'];
                      if ($statuscheck=="success") {
                             $updte= history::where('id', $status_check->id)->update(['status' => $statuscheck]);
                              
                       }else{
                            if ($statuscheck=="Not") {
                                
                                $updte= history::where('id', $status_check->id)->update(['status' => 'Fail']);
                            }
                        }

           }
          }
        }else{
          
        }


    $profile = User::find(Auth::id());;
    
    return view('user.profile', compact('profile'));
    }

   public function update(Request $request)
   {
  
     	$this->validate($request,
           [
               'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
               'name' => 'required'
            
           ]);
   		$profile = User::find(Auth::id());
        $profile['name'] = $request->name;


       if($request->hasFile('photo'))
        {
            if($profile->photo != 'nopic.png'){
            
        	$path = 'assets/images/profile/'.$profile->photo;
	        if(file_exists($path))
	        {
	            unlink($path);
	        }
            }
	        
            $profile['photo'] = uniqid().'.jpg';
            $request->photo->move('assets/images/profile',$profile['photo']);
        }
    
        $profile['country'] = $request->country;
        $profile['city'] = $request->city;
        $profile['address'] = $request->address;
        $profile['zip'] = $request->zip;
        $profile->save();
      
        return back()->with('success', 'Profile  Updated Successfully!');
    }
}
