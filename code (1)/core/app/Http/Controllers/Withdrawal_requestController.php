<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as input;
use App\General;
use App\User;
use App\IcoSetting;
use App\history;
use Auth;
use Hash;
use App\withdrawal_request;
use DB;
use App\wp_account;

class Withdrawal_requestController extends Controller
{
   public function index()
     {     

     	   $ico_setting = IcoSetting::first();
     	   $merchant  = $ico_setting->ETH_merchant_address;
     	   $private   = $ico_setting->ETH_merchant_private;
         $BTC_merchant_address = $ico_setting->BTC_merchant_address;
         $LTC_merchant_address = $ico_setting->LTC_merchant_address;
         $BTC_merchant_api_key = $ico_setting->BTC_merchant_api_key;
         $LTC_merchant_api_key = $ico_setting->LTC_merchant_api_key;
         $BTC_merchant_secret_pin = $ico_setting->BTC_merchant_secret_pin;
         $LTC_merchant_secret_pin = $ico_setting->LTC_merchant_secret_pin;
     	   $url = "https://world-meds.herokuapp.com/?task=getEther&ToAddress=".$merchant;  
     	   $cSession = curl_init();
     	   curl_setopt($cSession,CURLOPT_URL, $url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false);
           $Ether=curl_exec($cSession)/1000000000000000000;
           curl_close($cSession);
           //Getting balance of token (admin)
           $url = "https://world-meds.herokuapp.com/?task=getToken&ToAddress=".$merchant;
           $cSession = curl_init(); 
           curl_setopt($cSession,CURLOPT_URL, $url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false); 
           $Token=curl_exec($cSession)/1000000000000000000;
           curl_close($cSession);

        $btc_merchant_apikey= $BTC_merchant_api_key;
        $btc_url='https://block.io/api/v2/get_address_balance/?api_key='.$btc_merchant_apikey.'&addresses='.$BTC_merchant_address;
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
        $btc_pending_received_balance = $btc_user_address['data']['pending_received_balance'];



        $ltc_merchant_apikey= $LTC_merchant_api_key;
        $ltc_url='https://block.io/api/v2/get_address_balance/?api_key='.$ltc_merchant_apikey.'&addresses='.$LTC_merchant_address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ltc_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ltc_result = curl_exec($ch);
        if(curl_errno($ch)) {
         error_log('cURL error when connecting to ' . $ltc_result . ': ' . curl_error($ch));
        }
        curl_close($ch);
        $ltc_user_address=json_decode($ltc_result,true);
        $ltc_avail_balance=$ltc_user_address['data']['available_balance'];
        $ltc_pending_received_balance = $ltc_user_address['data']['pending_received_balance'];
       
        

          $fail_data = history::where('item', 'ETH')->where('status','fail')->orderBy('id', 'desc')->paginate(10);
          $fail_datas= history::where('item', 'TOKEN')->where('status','fail')->orderBy('id', 'desc')->paginate(10);
         
          $eth_widthrawal_request=DB::table('withdrawal_request')
                  ->join('users','users.id','=','withdrawal_request.user_id')
                  ->where('withdrawal_request.item','=','ETH')
                  ->select('withdrawal_request.*','users.name')
                  ->orderBy('withdrawal_request.id','desc')
                  ->paginate(10);
          $eth_widthrawal_request_token=DB::table('withdrawal_request')
                  ->join('users','users.id','=','withdrawal_request.user_id')
                  ->where('withdrawal_request.item','=','TOKEN')
                  ->select('withdrawal_request.*','users.name')
                  ->orderBy('withdrawal_request.id','desc')
                  ->paginate(10);  
          $bth_widthrawal_request=DB::table('withdrawal_request')
                  ->join('users','users.id','=','withdrawal_request.user_id')
                  ->where('withdrawal_request.item','=','BTC')
                  ->select('withdrawal_request.*','users.name')
                  ->orderBy('withdrawal_request.id','desc')
                  ->paginate(10);
          $lth_widthrawal_request=DB::table('withdrawal_request')
                  ->join('users','users.id','=','withdrawal_request.user_id')
                  ->where('withdrawal_request.item','=','LTC')
                  ->select('withdrawal_request.*','users.name')
                  ->orderBy('withdrawal_request.id','desc')
                  ->paginate(10);                      

                  
          return view('admin.withdrawal.index',compact('Ether','Token','fail_data','eth_widthrawal_request','fail_datas','eth_widthrawal_request_token','btc_avail_balance','btc_pending_received_balance','ltc_avail_balance','ltc_pending_received_balance','bth_widthrawal_request','lth_widthrawal_request'));

	     }
     function automatic (){
       $ico_setting = IcoSetting::first();
       $merchant  = $ico_setting->ETH_merchant_address;
       $private   = $ico_setting->ETH_merchant_private;
      if($private==null){
        return back()->with('alert', 'Enter the Merchant Private Key!!!');
      }else{
       if($merchant==null){
         return back()->with('alert', 'Enter the Merchant Address!!!'); 
       }else{ 
       $url = "https://world-meds.herokuapp.com/?task=getEther&ToAddress=".$merchant;  
         $cSession = curl_init();
         curl_setopt($cSession,CURLOPT_URL, $url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false);
           $Ether=curl_exec($cSession)/1000000000000000000;
           curl_close($cSession);
           //Getting balance of token (admin)
           $url = "https://world-meds.herokuapp.com/?task=getToken&ToAddress=".$merchant;
           $cSession = curl_init(); 
           curl_setopt($cSession,CURLOPT_URL, $url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false); 
           $Token=curl_exec($cSession)/1000000000000000000;
           curl_close($cSession);
       $amount_send= Input::get('amount_send');
       $address= Input::get('address');
       $id= Input::get('id');
       $user_id = Input::get('user_id');
       $NoEther=round($amount_send*1000000000000000000);
       $NoEther = number_format($NoEther, 0, '.', '');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://world-meds.herokuapp.com/?task=EtherTransfer&ToAddress=$address&NoEther=$NoEther&FromAddress=$merchant&PrivateKey=$private",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
     ));
       $response = curl_exec($curl);
       
       $AvlEthToBuy = $Ether - 0.0005;
      
       $result = str_replace('"','',$response,$count);
       $NoEther=$NoEther/10000;
       if ($count==2) {
          DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, 'Withdrawal','ETH',$NoEther,$amount_send,$result,'Pending']);
          DB::table('withdrawal_request')->where('id', $id)->delete();
         return back()->with('message', $result); 
         return back()->with('success', 'Request Successfully Sent.');
       }else{
         return back()->with('alert', ' Transaction Error');
       }


     }
   }
 }
  function reject(){
   $staus_hash= Input::get('staus_hash');
   if($staus_hash==null){
    return back()->with('alert', 'Enter the Reason of decline !!'); 
   }else{
     $staus_hash= Input::get('staus_hash');
     $status = Input::get('status');
     $amount_send= Input::get('amount_send');
     $user_id = Input::get('user_id');
     $task="withdrawal";
     $item='ETH';
     $id= Input::get('id');
     $total= Input::get('total');
     DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, $task,'ETH',$amount_send,$total,$staus_hash,$status]);
     DB::table('withdrawal_request')->where('id', $id)->delete();
     $sql_balance =  wp_account::where('user_id', $user_id)->get();
     $eth_balance = $sql_balance[0]->eth_balance;
     $update_balence = $eth_balance + $total;
     $updtes = wp_account::where('user_id', $user_id)->update(['eth_balance' => $update_balence]);

     return back()->with('success', 'Request Successfully Sent.');
   }
   
  }
  function fail(){

     $hash=Input::get('hash');
     $status=Input::get('status');
     $id=Input::get('id');
     $update_query = history::where('id', $id)->update(['hash' => $hash,'status' =>$status]);
      if($update_query){
        return back()->with('success', 'Done'); 
      }else{
        return back()->with('alert', 'Try Again'); 
      }
                                      
  }

   function automatictoken (){
     $ico_setting = IcoSetting::first();
       $merchant  = $ico_setting->ETH_merchant_address;
       $private   = $ico_setting->ETH_merchant_private;
      if($private==null){
        return back()->with('alert', 'Enter the Merchant Private Key!!!');
      }else{
       if($merchant==null){
         return back()->with('alert', 'Enter the Merchant Address!!!'); 
       }else{ 
       $url = "https://world-meds.herokuapp.com/?task=getEther&ToAddress=".$merchant;  
         $cSession = curl_init();
         curl_setopt($cSession,CURLOPT_URL, $url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false);
           $Ether=curl_exec($cSession)/1000000000000000000;
           curl_close($cSession);
           //Getting balance of token (admin)
           $url = "https://world-meds.herokuapp.com/?task=getToken&ToAddress=".$merchant;
           $cSession = curl_init(); 
           curl_setopt($cSession,CURLOPT_URL, $url);
           curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
           curl_setopt($cSession,CURLOPT_HEADER, false); 
           $Token=curl_exec($cSession)/1000000000000000000;
           curl_close($cSession);
       $amount_send= Input::get('amount_send');
       $address= Input::get('address');
       $id= Input::get('id');
       $user_id = Input::get('user_id');
       $total_token = Input::get('total');
       $NoToken=round($amount_send*1000000000000000000);
       $NoToken = number_format($NoToken, 0, '.', '');
      
      if($Ether >= 0.0005 && $Token >= $amount_send ){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://world-meds.herokuapp.com/?task=TokenTransfer&ToAddress=$address&NoToken=$NoToken&FromAddress=$merchant&PrivateKey=$private",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
     ));
             $response = curl_exec($curl);
             $result = str_replace('"','',$response,$count);
             $NoToken=$NoToken/10000;
             if ($count==2) {
               DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, 'Withdrawal','TOKEN',$amount_send,$total_token,$result,'Pending']);
          DB::table('withdrawal_request')->where('id', $id)->delete();
         return back()->with('messagetoken', $result); 
         return back()->with('success', 'Request Successfully Sent.');
             }else{
              return back()->with('alert', ' Transaction Error'); 
             }




        }else{
         return back()->with('alert', 'Make sure you have enough Token for transfer and Ether for gas(Approx 0.0005 Ether'); 
        }

       

      }
     
      


     }
   }


   function rejecttoken(){
     $staus_hash= Input::get('staus_hash');
   if($staus_hash==null){
    return back()->with('alert', 'Enter the Reason of decline !!'); 
   }else{
      $staus_hash= Input::get('staus_hash');
     $status = Input::get('status');
     $amount_send= Input::get('amount_send');
     $user_id = Input::get('user_id');
     $task="withdrawal";
     $item='TOKEN';
     $id= Input::get('id');
     $total= Input::get('total');
     DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, $task,'TOKEN',$amount_send,$total,$staus_hash,$status]);
     DB::table('withdrawal_request')->where('id', $id)->delete();
     $sql_balance =  wp_account::where('user_id', $user_id)->get();
     $token_balance = $sql_balance[0]->token_balance;
     $update_balence = $token_balance + $total;
     $updtes = wp_account::where('user_id', $user_id)->update(['token_balance' => $update_balence]);

     return back()->with('success', 'Request Successfully Sent.');
   }
   }
    function faileth(){

     $hash=Input::get('hash');
     $status=Input::get('status');
     $id=Input::get('id');
     $update_query = history::where('id', $id)->update(['hash' => $hash,'status' =>$status]);
      if($update_query){
        return back()->with('success', 'Done'); 
      }else{
        return back()->with('alert', 'Try Again'); 
      }
                                      
  }

   function failtoken(){

     $hash=Input::get('hash');
     $status=Input::get('status');
     $id=Input::get('id');
     $update_query = history::where('id', $id)->update(['hash' => $hash,'status' =>$status]);
      if($update_query){
        return back()->with('success', 'Done'); 
      }else{
        return back()->with('alert', 'Try Again'); 
      }
                                      
  }

 function btcreject(){
   $staus_hash= Input::get('staus_hash');
   if($staus_hash==null){
    return back()->with('alert', 'Enter the Reason of decline !!'); 
   }else{
     $staus_hash= Input::get('staus_hash');
     $status = Input::get('status');
     $amount_send= Input::get('amount_send');
     $user_id = Input::get('user_id');
     $task="withdrawal";
     $item='BTC';
     $id= Input::get('id');
     $total= Input::get('total');
     DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, $task,'BTC',$amount_send,$total,$staus_hash,$status]);
     DB::table('withdrawal_request')->where('id', $id)->delete();
     $sql_balance =  wp_account::where('user_id', $user_id)->get();
     $btc_balance = $sql_balance[0]->btc_balance;
     $update_balence = $btc_balance + $total;
     $updtes = wp_account::where('user_id', $user_id)->update(['btc_balance' => $update_balence]);

     return back()->with('success', 'Request Successfully Sent.');
   }
   
  }
   function ltcreject(){
   $staus_hash= Input::get('staus_hash');
   if($staus_hash==null){
    return back()->with('alert', 'Enter the Reason of decline !!'); 
   }else{
     $staus_hash= Input::get('staus_hash');
     $status = Input::get('status');
     $amount_send= Input::get('amount_send');
     $user_id = Input::get('user_id');
     $task="withdrawal";
     $item='LTC';
     $id= Input::get('id');
     $total= Input::get('total');
     DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, $task,'LTC',$amount_send,$total,$staus_hash,$status]);
     DB::table('withdrawal_request')->where('id', $id)->delete();
     $sql_balance =  wp_account::where('user_id', $user_id)->get();
     $ltc_balance = $sql_balance[0]->ltc_balance;
     $update_balence = $ltc_balance + $total;
     $updtes = wp_account::where('user_id', $user_id)->update(['ltc_balance' => $update_balence]);

     return back()->with('success', 'Request Successfully Sent.');
   }
   
  }


  function btcautomatic(){
    $ico_setting = IcoSetting::first();
    $BTC_merchant_address = $ico_setting->BTC_merchant_address;
    $BTC_merchant_api_key = $ico_setting->BTC_merchant_api_key;
    $BTC_merchant_secret_pin = $ico_setting->BTC_merchant_secret_pin;
    if($BTC_merchant_address==null){
      return back()->with('alert', 'Enter the Merchant Address for BTC!!!');
    }else{
      if($BTC_merchant_api_key==null){
         return back()->with('alert', 'Enter the Merchant Api key!!!'); 
      }else{
        if($BTC_merchant_secret_pin==null){
          return back()->with('alert', 'Enter the Merchant Secret key!!!');
        }else{
           $amount_send= Input::get('amount_send');
           $address= Input::get('address');
           $id= Input::get('id');
           $user_id = Input::get('user_id');
            $curl = curl_init();
          $url1='https://block.io/api/v2/withdraw_from_addresses/?api_key='.$BTC_merchant_api_key.'&from_addresses='.$BTC_merchant_address.'&to_addresses='.$address.'&amounts='.$amount_send.'&pin='.$BTC_merchant_secret_pin;
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
          
          $tx_id = $check_max_withdrawal['data']['txid'];
             
          if ($tx_id!=null) {
          DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, 'Withdrawal','BTC',$amount_send,$amount_send,$tx_id,'Pending']);
          DB::table('withdrawal_request')->where('id', $id)->delete();
         return back()->with('btchash', $tx_id); 
         return back()->with('success', 'Request Successfully Sent.');
         }else{
         return back()->with('alert', ' Transaction Error');
       }  


        }
      }
    }
  }

  function ltcautomatic(){
  $ico_setting = IcoSetting::first();
  
  $LTC_merchant_address = $ico_setting->LTC_merchant_address;
  $LTC_merchant_api_key = $ico_setting->LTC_merchant_api_key;
  $LTC_merchant_secret_pin = $ico_setting->LTC_merchant_secret_pin;
  if($LTC_merchant_address==null){
      return back()->with('alert', 'Enter the Merchant Address for LTC!!!');
    }else{
      if($LTC_merchant_api_key==null){
         return back()->with('alert', 'Enter the Merchant Api key!!!'); 
      }else{
        if($LTC_merchant_secret_pin==null){
          return back()->with('alert', 'Enter the Merchant Secret key!!!');
        }else{
          $amount_send= Input::get('amount_send');
          $address= Input::get('address');
          $id= Input::get('id');
          $user_id = Input::get('user_id');
           $amount_send= Input::get('amount_send');
           $address= Input::get('address');
           $id= Input::get('id');
           $user_id = Input::get('user_id');
            $curl = curl_init();
          $url1='https://block.io/api/v2/withdraw_from_addresses/?api_key='.$LTC_merchant_api_key.'&from_addresses='.$LTC_merchant_address.'&to_addresses='.$address.'&amounts='.$amount_send.'&pin='.$LTC_merchant_secret_pin;
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
          $tx_id = $check_max_withdrawal['data']['txid'];
             
          if ($tx_id!=null) {
          DB::insert('insert into history (user_id, task, item,amount,amount_by,hash,status) values (?, ?,?,?,?,?,?)', [$user_id, 'Withdrawal','LTC',$amount_send,$amount_send,$tx_id,'Pending']);
          DB::table('withdrawal_request')->where('id', $id)->delete();
         return back()->with('ltchash', $tx_id); 
         return back()->with('success', 'Request Successfully Sent.');
         }else{
         return back()->with('alert', ' Transaction Error');
       }  
        }
      }
    }

}	    
} 
