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
use App\withdrawal_request;
use Auth;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as input;
use Session;
use App\IcoSetting;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','ckstatus']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
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
        
        $url = "https://world-meds.herokuapp.com/?task=startsAt";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $startsAt=curl_exec($cSession);
                    curl_close($cSession); 
         $url = "https://world-meds.herokuapp.com/?task=endsAt";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $endsAt=curl_exec($cSession);
                    curl_close($cSession);                                    
                  
        
       
    $balance = wp_account::where('user_id', Auth::id())->get();
      if($balance == null){
       $eth_balance =0;
       $token_balance=0; 
       $btc_balance =0;
       $ltc_balance=0; 
      }else{
       $eth_balance =$balance[0]['eth_balance'];
       $token_balance=$balance[0]['token_balance'];
       $btc_balance =$balance[0]['btc_balance'];
       $ltc_balance =$balance[0]['ltc_balance'];
       $foo = $eth_balance;
       $eth_balance =  number_format((float)$foo, 6, '.', '');
       $foo1 = $token_balance; 
       $token_balance =  number_format((float)$foo1, 2, '.', '');
       $foo2 = $btc_balance;
       $btc_balance =  number_format((float)$foo2, 6, '.', '');
       $foo3 = $ltc_balance;
       $ltc_balance =  number_format((float)$foo3, 6, '.', '');
    }
       $ico_setting = IcoSetting::first();
       $ETH_Merchant_Address = $ico_setting->ETH_merchant_address;
       $ico_sale_text = $ico_setting->IcoSaleText;
       $ETH_raised =  $ico_setting->ETH_raised;
       $TokenRemain = $ico_setting->Token_sold;
       $current_date = date('M d, Y G:i:s');
       $current_time = strtotime($current_date);
       if($current_time > $startsAt ){
       if($current_time > $endsAt){
            $countdown= $endsAt ;
                $text = 'is Expired';
        }else{
                $countdown= $endsAt ;
                $text = 'Ends In';

        }
    }else{
        $text = 'Starts In';
        $countdown= $startsAt;
       
    }


      
      $nexts = Ico::where('status','!=',2)->where('status','!=',3)->get();
      return view('user.home', compact('nexts','eth_balance','token_balance','ltc_balance','btc_balance','ETH_raised','ico_sale_text','startsAt','endsAt','current_time','TokenRemain','countdown','text'));
    }

    public function myCoin()
    {
      $coins = Sell::where('user_id', Auth::id())->where('status', 1)->get();
      return view('user.mycoin', compact('coins'));
    }

    public function buyIco()
    {
        $gates = Gateway::where('status', 1)->get();
        $ico = Ico::where('status',1)->first();
        return view('user.buy', compact('gates','ico'));
    }

    public function buyPreview(Request $request)
    {
      $this->validate($request,
            [
                'amount' => 'required',
                'gateway' => 'required',
            ]);
         $ico = Ico::where('status',1)->first();
         $total = $request->amount + $ico->sold;
         if ($request->amount <=0 || $total > $ico->quant) 
         {
            return back()->with('alert', 'Invalid Amount');
         }
         else
         {
            $gate = Gateway::findOrFail($request->gateway);
            if(is_null($gate))
            {
              return back()->with('alert', 'Please Select a Payment Gateway');
            }
            else
            {
              $ico = Ico::where('status',1)->first();

              if ($gate->id == 3 || $gate->id == 6 || $gate->id == 7 || $gate->id == 8) 
              {
                  $all = file_get_contents("https://blockchain.info/ticker");
                  $res = json_decode($all);
                  $btcrate = $res->USD->last;

                  $amount = intval($request->amount);
                  $usd = $ico->price*$amount;
                  $btcamount = $usd/$btcrate;
                  $btc = round($btcamount, 8);

                  $sell['user_id'] = Auth::id();
                  $sell['ico_id'] = $ico->id;
                  $sell['gateway_id'] = $gate->id;
                  $sell['amount'] = $amount;
                  $sell['bcam'] = $btc;
                  $sell['status'] = 0;
                  $sell['trx'] = str_random(16);
                  Sell::create($sell);
                  Session::put('Track', $sell['trx']);

                  return view('user.preview', compact('btc','gate','ico','amount'));
              }
              else
              {
                  $amount = intval($request->amount);
                  $usd = $ico->price*$amount;

                  $sell['user_id'] = Auth::id();
                  $sell['ico_id'] = $ico->id;
                  $sell['gateway_id'] = $gate->id;
                  $sell['amount'] = $amount;
                  $sell['status'] = 0;
                  $sell['trx'] = str_random(16);
                  Sell::create($sell);
                  Session::put('Track', $sell['trx']);

                  return view('user.preview', compact('usd','gate','ico','amount'));
              }
            }
          }
    }

    public function referal()
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
        $refers = User::where('refer', Auth::id())->paginate(10);
        return view('user.refer', compact('refers'));
    }

    //Change password
    public function changepass()
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
        
        
        $user = User::find(Auth::id());
        return view('auth.passwords.change', compact('user'));
    }

    public function chnpass()
    {
      $user = User::find(Auth::id());

      if(Hash::check(Input::get('passwordold'), $user['password']) && Input::get('password') == Input::get('password_confirmation'))
      {
        $user->password = bcrypt(Input::get('password'));
        $user->save();

        $msg =  'Password Changed Successfully';
        send_email($user->email, $user->username, 'Password Changed', $msg);
        $sms =  'Password Changed Successfully';
        send_sms($user->mobile, $sms);

        return back()->with('success', 'Password Changed');
      }
      else 
      {
          return back()->with('alert', 'Password Not Changed');
      }
    }


    public function google2fa()
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
        $gnl = General::first();
        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl(Auth::user()->username.'@'.$gnl->title, $secret);

        $prevcode = Auth::user()->secretcode;
        $prevqr = $ga->getQRCodeGoogleUrl(Auth::user()->username.'@'.$gnl->title, $prevcode);

        return view('user.goauth.create', compact('secret','qrCodeUrl','prevcode','prevqr'));
    }

    public function create2fa(Request $request)
    {
         $user = User::find(Auth::id());
        
        $this->validate($request,
            [
                'key' => 'required',
                'code' => 'required',
            ]);

        $ga = new GoogleAuthenticator();

        $secret = $request->key;
        $oneCode = $ga->getCode($secret); 
        $userCode = $request->code;
        if ($oneCode == $userCode) 
        { 
            $user['secretcode'] = $request->key;
            $user['tauth'] = 1;
            $user['tfver'] = 1;
            $user->save();

            $msg =  'Google Two Factor Authentication Enabled Successfully';
            send_email($user->email, $user->username, 'Google 2FA', $msg);
            $sms =  'Google Two Factor Authentication Enabled Successfully';
            send_sms($user->mobile, $sms);

            return back()->with('success', 'Google Authenticator Enabeled Successfully');
        }
        else 
        {
          return back()->with('alert', 'Wrong Verification Code');
        }
              
    }

    public function disable2fa(Request $request)
    {
      $this->validate($request,
        [
            'code' => 'required',
        ]);

      $user = User::find(Auth::id());
      $ga = new GoogleAuthenticator();

      $secret = $user->secretcode;
      $oneCode = $ga->getCode($secret); 
      $userCode = $request->code;

      if ($oneCode == $userCode) 
      { 
        $user = User::find(Auth::id());
        $user['tauth'] = 0;
        $user['tfver'] = 1;
        $user['secretcode'] = '0';
        $user->save();

        $msg =  'Google Two Factor Authentication Disabled Successfully';
        send_email($user->email, $user->username, 'Google 2FA', $msg);
        $sms =  'Google Two Factor Authentication Disabled Successfully';
        send_sms($user->mobile, $sms);

        return back()->with('success', 'Two Factor Authenticator Disable Successfully');
      } 
      else 
      {
        return back()->with('alert', 'Wrong Verification Code');
      }
       
    }

public function Wallet()
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
      $balance = wp_account::where('user_id', Auth::id())->get();
      if($balance == null){
       $eth_balance =0;
       $token_balance=0; 
       $btc_balance =0;
       $ltc_balance =0;
       $eth_user_address= $address[0]->eth_address;
       $btc_user_address= $address[0]->btc_address;
       $ltc_user_address= $address[0]->ltc_address;
      }else{
       $eth_balance =$balance[0]['eth_balance'];
       $token_balance=$balance[0]['token_balance'];
       $btc_balance =$balance[0]['btc_balance'];
       $ltc_balance =$balance[0]['ltc_balance'];
        $foo = $eth_balance;
       $eth_balance =  number_format((float)$foo, 6, '.', '');
       $foo1 = $token_balance; 
       $token_balance =  number_format((float)$foo1, 2, '.', '');
       $foo2 = $btc_balance;
       $btc_balance =  number_format((float)$foo2, 6, '.', '');
       $foo3 = $ltc_balance;
       $ltc_balance =  number_format((float)$foo3, 6, '.', '');
       $eth_user_address= $address[0]->eth_address;
       $btc_user_address= $address[0]->btc_address;
       $ltc_user_address= $address[0]->ltc_address;
       }
       $ico_setting = IcoSetting::first();
       $ETH_Merchant_fee = $ico_setting->eth_fees;
       if($ETH_Merchant_fee>0){
        $fee= $ETH_Merchant_fee;
       }else{
         $fee=0;
       }

       $btcfee=$ico_setting->btc_fees;
       $ltcfee=$ico_setting->ltc_fees;;
       $history = eth_deposit::where('user_id', Auth::id())->paginate(10);
       $withdrawal_request = withdrawal_request::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
      
        
        return view('wallet.index', compact('eth_balance','token_balance','btc_balance','ltc_balance','eth_user_address','btc_user_address','ltc_user_address','user_ID','fee','history','withdrawal_request','btcfee','ltcfee'));
    }


    public function Transaction()
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
        $history = history::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(10);
        return view('Transaction.index',compact('history'));
    }

    public function buytoken()
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
       $url = "https://world-meds.herokuapp.com/?task=tokensSold";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $TokenRemain=curl_exec($cSession)/100000000;
                    curl_close($cSession);
       $url = "https://world-meds.herokuapp.com/?task=weiRaised";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $ETH_raised=curl_exec($cSession)/100000000;
                    curl_close($cSession);
        $url = "https://world-meds.herokuapp.com/?task=startsAt";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $startsAt=curl_exec($cSession);
                    curl_close($cSession); 
         $url = "https://world-meds.herokuapp.com/?task=endsAt";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $endsAt=curl_exec($cSession);
                    curl_close($cSession);
         $url = "https://world-meds.herokuapp.com/?task=TokenPerETH";
         $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $TokenPerETHs=curl_exec($cSession);
                    curl_close($cSession);           

       $balance = wp_account::where('user_id', Auth::id())->get();
      if($balance == null){
      $eth_balance =0;
       $token_balance=0; 
       $btc_balance =0;
       $ltc_balance =0;
       $eth_user_address= $address[0]->eth_address;
       $btc_user_address= $address[0]->btc_address;
       $ltc_user_address= $address[0]->ltc_address;
      }else{
      $eth_balance =$balance[0]['eth_balance'];
       $token_balance=$balance[0]['token_balance'];
       $btc_balance =$balance[0]['btc_balance'];
       $ltc_balance =$balance[0]['ltc_balance'];
        $foo = $eth_balance;
       $eth_balance =  number_format((float)$foo, 6, '.', '');
       $foo1 = $token_balance; 
       $token_balance =  number_format((float)$foo1, 2, '.', '');
       $foo2 = $btc_balance;
       $btc_balance =  number_format((float)$foo2, 6, '.', '');
       $foo3 = $ltc_balance;
       $ltc_balance =  number_format((float)$foo3, 6, '.', '');
       $eth_user_address= $address[0]->eth_address;
       $btc_user_address= $address[0]->btc_address;
       $ltc_user_address= $address[0]->ltc_address;
       }
       $ico_setting = IcoSetting::first();
       $ETH_Merchant_fee = $ico_setting->eth_fees;
       if($ETH_Merchant_fee>0){
        $fee= $ETH_Merchant_fee;
       }else{
         $fee=0;
       }
       $btcfee=$ico_setting->btc_fees;
       $ltcfee=$ico_setting->ltc_fees;
        $ico_setting = IcoSetting::first();
       $ETH_Merchant_Address = $ico_setting->ETH_merchant_address;
       $ico_sale_text = $ico_setting->IcoSaleText;
       $current_date = date('M d, Y G:i:s');
       $current_time = strtotime($current_date);
       if($current_time > $startsAt ){
       if($current_time > $endsAt){
            $countdown= $endsAt ;
                $text = 'is Expired';
        }else{
                $countdown= $endsAt ;
                $text = 'Ends In';

        }
    }else{
        $text = 'Starts In';
        $countdown= $startsAt;
       
    }
       $history = eth_deposit::where('user_id', Auth::id())->paginate(10);
       $withdrawal_request = withdrawal_request::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

       $TokenPerBTC=$ico_setting->TokenPerBTC;
       $TokenPerLTC=$ico_setting->TokenPerLTC;
      
        
        return view('buytoken.index', compact('eth_balance','token_balance','ltc_balance','btc_balance','eth_user_address','btc_user_address','ltc_user_address','user_ID','fee','btcfee','ltcfee','history','withdrawal_request','ETH_raised','ico_sale_text','startsAt','endsAt','current_time','TokenRemain','countdown','text','TokenPerETHs','TokenPerLTC','TokenPerBTC'));
        
    }



 /* wallet Post route function */

 function token_submit(){

    $address=Input::get('token_address');
    $amount_req=abs(Input::get('token_amount'));
    $password=Input::get('token_password');
    $amount_send=$amount_req;
    $token_balance=Input::get('token_balance');
    $refund_token=$token_balance-$amount_req;
    $hash="";
    $status=0;
    $item="TOKEN";
    $user = User::find(Auth::id());
    $user_id = $user['id'];
    if(Hash::check(Input::get('token_password'), $user['password']))
      {
        
       if($token_balance>=$amount_req && $amount_req>0 ){
          
            
             
             $insert_query=DB::insert('insert into withdrawal_request (user_id,amount_send,fee,total,hash,status,address,item) values (?,?,?,?,?,?,?,?)', [$user_id,$amount_send,0,$amount_req,$hash,$status,$address,$item]); 
              if($insert_query){
                $update_query = wp_account::where('user_id', $user_id)->update(['token_balance' => $refund_token]);
                return back()->with('success', 'Withdraw Request Successfully Sent');
              }     

        }else{
          return back()->with('alert', 'Not Sufficient Balance!!!'); 
        }
      }else{
        return back()->with('alert', 'Password is Wrong!!!');  
        }
   
 }

 function eth_submit(){
     $address=Input::get('eth_address');
     $amount_req=abs(Input::get('eth_amount'));
     $password=Input::get('eth_password');
     $fee = Input::get('fee');
     $amount_send=$amount_req-$fee;
     $eth_balance = Input::get('eth_balance');
     $refund_eth=$eth_balance-$amount_req;
     $hash="";
     $status=0;
     $item="ETH";
     $user = User::find(Auth::id());
     $user_id = $user['id'];
     if(Hash::check(Input::get('eth_password'), $user['password']))
      {
         if($eth_balance>=$amount_req && $amount_req>0){
          $insert_query=DB::insert('insert into withdrawal_request (user_id,amount_send,fee,total,hash,status,address,item) values (?,?,?,?,?,?,?,?)', [$user_id,$amount_send,$fee,$amount_req,$hash,$status,$address,$item]);
          if($insert_query){
                $update_query = wp_account::where('user_id', $user_id)->update(['eth_balance' => $refund_eth]);
                return back()->with('success', 'Withdraw Request Successfully Sent');
              }  
         }else{
           return back()->with('alert', 'Not Sufficient Balance!!!'); 
         }
      }else{
         return back()->with('alert', 'Password is Wrong!!!');
      }



 }

 function buy_token(){
  
  $user_ID = Auth::user()->id;
  
      $balance = wp_account::where('user_id', Auth::id())->get();
      if($balance == null){
       $eth_balance =0;
       $token_balance=0; 
       $btc_balance=0; 
       $ltc_balance=0; 
      }else{
       $eth_balance =$balance[0]['eth_balance'];
       $token_balance=$balance[0]['token_balance'];
       $btc_balance=$balance[0]['btc_balance'];
       $ltc_balance=$balance[0]['ltc_balance'];
      }
       $ico_setting = IcoSetting::first();
       $eth_fee = $ico_setting->eth_fees;
       if($eth_fee>0){
        $fee= $eth_fee;
        $btc_fee = $ico_setting->btc_fees;
        $ltc_fee = $ico_setting->ltc_fees;
       }else{
         $fee=0;
         $btc_fee =0;
         $ltc_fee =0;
       }
      $refer_bonus = $ico_setting->referral_bonus/100;

      $amount = abs(Input::get('NoEther')); 
      $coinname = Input::get('select_coin');

      if($coinname=="ETH"){
      $url = "https://world-meds.herokuapp.com/?task=TokenPerETH";
      $cSession = curl_init(); 
                    curl_setopt($cSession,CURLOPT_URL,$url);
                    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($cSession,CURLOPT_HEADER, false); 
                    $TokenPerETH=curl_exec($cSession);
                    curl_close($cSession);
      $total = $amount + $fee; 
      
      if($amount>0){
      if ($eth_balance >= $total) {
      $token = $amount * $TokenPerETH;
      $ETH_raised = $ico_setting->ETH_raised;
      $ETH_raised += $amount;
      $update_query = IcoSetting::where('id', 1)->update(['ETH_raised' => $ETH_raised]);
      $Token_sold = $ico_setting->Token_sold;
      $Token_sold += $token;
      $update_query = IcoSetting::where('id', 1)->update(['Token_sold' => $Token_sold]);
      $update_eth = $eth_balance - $total;
      $update_buy = $token_balance + $token;
      $query1 = wp_account::where('user_id', $user_ID)->update(['eth_balance' => $update_eth,'token_balance' =>$update_buy]);
      $query2 = DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?,?,?,?,?,?,?)', [$user_ID,'Buy','ETH',$token,$total,'-','Success']);
      $parent_id = Auth::user()->refer;
       if($parent_id==0){
         
       }else{
         $self_name = Auth::user()->name;
         $referbalance = wp_account::where('user_id', $parent_id)->get();
         $refertoken_balance=$referbalance[0]['token_balance']; 
         $refer_token = $token*$refer_bonus;
         $parent_token_update =  $refertoken_balance + $refer_token;
         $query3 = wp_account::where('user_id', $parent_id)->update(['token_balance' =>$parent_token_update]);
         $query2 = DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?,?,?,?,?,?,?)', [$user_ID,'Buy','Referral',$refer_token,$refer_token,$self_name,'Success']);
       }
       return back()->with('success', 'Token buy Sucessfully.!!!');
      }else{
       return back()->with('alert', 'Not Enough Balance!!!'); 
      }  
      }else{
       return back()->with('alert', 'Value must be Greter than Zero !!!'); 
      }             
     
      }else if($coinname=="BTC"){
      $ico_setting = IcoSetting::first();
      $TokenPerBTC = $ico_setting->TokenPerBTC;
      $total = $amount + $btc_fee;
      if($amount>0){
       if ($btc_balance >= $total) {
      $token = $amount * $TokenPerBTC;
      $Token_sold = $ico_setting->Token_sold;
      $Token_sold += $token;
      $update_query = IcoSetting::where('id', 1)->update(['Token_sold' => $Token_sold]);
      $update_btc = $btc_balance - $total;
      $update_buy = $token_balance + $token;
      $query1 = wp_account::where('user_id', $user_ID)->update(['btc_balance' => $update_btc,'token_balance' =>$update_buy]);
      $query2 = DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?,?,?,?,?,?,?)', [$user_ID,'Buy','BTC',$token,$total,'-','Success']);
      $parent_id = Auth::user()->refer;
       if($parent_id==0){
         
       }else{
         $self_name = Auth::user()->name;
         $referbalance = wp_account::where('user_id', $parent_id)->get();
         $refertoken_balance=$referbalance[0]['token_balance']; 
         $refer_token = $token*$refer_bonus;
         $parent_token_update =  $refertoken_balance + $refer_token;
         $query3 = wp_account::where('user_id', $parent_id)->update(['token_balance' =>$parent_token_update]);
         $query2 = DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?,?,?,?,?,?,?)', [$user_ID,'Buy','Referral',$refer_token,$refer_token,$self_name,'Success']);
       }
       return back()->with('success', 'Token buy Sucessfully.!!!');
      }else{
       return back()->with('alert', 'Not Enough Balance!!!'); 
      }  
      }else{
       return back()->with('alert', 'Value must be Greter than Zero !!!'); 
      }
      }else if($coinname=="LTC"){
      $ico_setting = IcoSetting::first();
      $TokenPerLTC = $ico_setting->TokenPerLTC; 
      $total = $amount + $ltc_fee;
      if($amount>0){
       if ($ltc_balance >= $total) {
       $token = $amount * $TokenPerLTC;
       $Token_sold = $ico_setting->Token_sold;
       $Token_sold += $token;
       $update_query = IcoSetting::where('id', 1)->update(['Token_sold' => $Token_sold]);
       $update_ltc = $ltc_balance - $total;
       $update_buy = $token_balance + $token;
       $query1 = wp_account::where('user_id', $user_ID)->update(['btc_balance' => $update_ltc,'token_balance' =>$update_buy]);
       $query2 = DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?,?,?,?,?,?,?)', [$user_ID,'Buy','LTC',$token,$total,'-','Success']);
       $parent_id = Auth::user()->refer;
       if($parent_id==0){
         
       }else{
         $self_name = Auth::user()->name;
         $referbalance = wp_account::where('user_id', $parent_id)->get();
         $refertoken_balance=$referbalance[0]['token_balance']; 
         $refer_token = $token*$refer_bonus;
         $parent_token_update =  $refertoken_balance + $refer_token;
         $query3 = wp_account::where('user_id', $parent_id)->update(['token_balance' =>$parent_token_update]);
         $query2 = DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?,?,?,?,?,?,?)', [$user_ID,'Buy','Referral',$refer_token,$refer_token,$self_name,'Success']);
       }
       return back()->with('success', 'Token buy Sucessfully.!!!');
      }else{
       return back()->with('alert', 'Not Enough Balance!!!'); 
      }  
      }else{
       return back()->with('alert', 'Value must be Greter than Zero !!!'); 
      }
      }else{
       return back()->with('alert', 'Some thing is wrong !!');  
      }

      

      



 }

function btc_submit(){
     $address=Input::get('btc_address');
     $amount_req=abs(Input::get('btc_amount'));
     $password=Input::get('btc_password');
     $fee = Input::get('fee');
     $amount_send=$amount_req-$fee;
     $btc_balance = Input::get('btc_balance');
     $refund_btc=$btc_balance-$amount_req;
     $hash="";
     $status=0;
     $item="BTC";
     $user = User::find(Auth::id());
     $user_id = $user['id'];
     if(Hash::check(Input::get('btc_password'), $user['password']))
      {
         if($btc_balance>=$amount_req && $amount_req>0){
          $insert_query=DB::insert('insert into withdrawal_request (user_id,amount_send,fee,total,hash,status,address,item) values (?,?,?,?,?,?,?,?)', [$user_id,$amount_send,$fee,$amount_req,$hash,$status,$address,$item]);
          if($insert_query){
                $update_query = wp_account::where('user_id', $user_id)->update(['btc_balance' => $refund_btc]);
                return back()->with('success', 'Withdraw Request For BTC Successfully Sent');
              }  
         }else{
           return back()->with('alert', 'Not Sufficient Balance!!!'); 
         }
      }else{
         return back()->with('alert', 'Password is Wrong!!!');
      }



 }

function ltc_submit(){
     $address=Input::get('ltc_address');
     $amount_req=abs(Input::get('ltc_amount'));
     $password=Input::get('ltc_password');
     $fee = Input::get('fee');
     $amount_send=$amount_req-$fee;
     $ltc_balance = Input::get('ltc_balance');
     $refund_ltc=$ltc_balance-$amount_req;
     $hash="";
     $status=0;
     $item="LTC";
     $user = User::find(Auth::id());
     $user_id = $user['id'];
     if(Hash::check(Input::get('ltc_password'), $user['password']))
      {
         if($ltc_balance>=$amount_req && $amount_req>0){
          $insert_query=DB::insert('insert into withdrawal_request (user_id,amount_send,fee,total,hash,status,address,item) values (?,?,?,?,?,?,?,?)', [$user_id,$amount_send,$fee,$amount_req,$hash,$status,$address,$item]);
          if($insert_query){
                $update_query = wp_account::where('user_id', $user_id)->update(['ltc_balance' => $refund_ltc]);
                return back()->with('success', 'Withdraw Request For LTC Successfully Sent');
              }  
         }else{
           return back()->with('alert', 'Not Sufficient Balance!!!'); 
         }
      }else{
         return back()->with('alert', 'Password is Wrong!!!');
      }



 }



}
