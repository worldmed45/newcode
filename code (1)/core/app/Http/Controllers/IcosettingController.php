<?php

namespace App\Http\Controllers;
use DB;
use App\Gateway;
use App\IcoSetting;
use Auth;
use Hash;


use Illuminate\Http\Request;

class IcosettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          
        
        $ico_setting = IcoSetting::first();
        return view('admin.icosetting.index', compact('ico_setting'));
    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gateway  $gateway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       $ico_setting  =  IcoSetting::first();

          $ico_setting['Hard_cap'] = $request->hard_cap;
          $ico_setting['Soft_cap'] = $request->soft_cap;
        
          $ico_setting['Token_sold'] = $request->token_sold;
          $ico_setting['ETH_raised'] = $request->eth_raised;
         
          $ico_setting['referral_bonus'] = $request->referral_bonus;
          $ico_setting['IcoSaleText'] = $request->IcoSaleText;
          $ico_setting['eth_fees'] = $request->eth_fees;
          $ico_setting['btc_fees'] = $request->btc_fees;
          $ico_setting['ltc_fees'] = $request->ltc_fees;
          $ico_setting['TokenPerBTC'] = $request->Tokenperbtc;
          $ico_setting['TokenPerLTC'] = $request->Tokenperltc;
          $ico_setting['ETH_merchant_address'] = $request->ETH_merchant_address ;
          $ico_setting['ETH_merchant_private'] = $request->ETH_merchant_private;
          $ico_setting['BTC_merchant_address'] = $request->BTC_merchant_address;
          $ico_setting['LTC_merchant_address'] = $request->LTC_merchant_address;
          $ico_setting['BTC_merchant_api_key'] = $request->BTC_merchant_api_key;
          $ico_setting['LTC_merchant_api_key'] = $request->LTC_merchant_api_key;
          $ico_setting['BTC_merchant_secret_pin'] = $request->BTC_merchant_secret_pin;
          $ico_setting['LTC_merchant_secret_pin'] = $request->LTC_merchant_secret_pin;

          $ico_setting->save();
          return back()->with('success', 'ICO Settings Updated Successfully!');
         
    }

    public function setcrowddetting(){
        $setcrowddetting = IcoSetting::first();
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
                    
          $TokenPerETHshash = $setcrowddetting->TokenPerETHhash;  
          $url = "https://world-meds.herokuapp.com/?task=confirm&hash=".$TokenPerETHshash;
                $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL,$url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $tokenperethstatus=curl_exec($cSession);
                curl_close($cSession);
            $statusone = str_replace('"','',$tokenperethstatus,$count);     
           if ($statusone=="Done") {
             $a=1;
           }else if($statusone=="Panding"){
             $a=2;
           }else if($statusone=="Not"){
             $a=3;
           }else{
             $a=4;
           }

          $starttimehash = $setcrowddetting->starttimehash;  
          $urls = "https://world-meds.herokuapp.com/?task=confirm&hash=".$starttimehash;
                $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL,$urls);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $starttimehashstatus=curl_exec($cSession);
                curl_close($cSession);
            $statustwo = str_replace('"','',$starttimehashstatus,$count);     
           if ($statustwo=="Done") {
             $b=1;
           }else if($statustwo=="Panding"){
             $b=2;
           }else if($statusone=="Not"){
             $b=3;
           }else{
             $b=4;
           }

           $endtimehash = $setcrowddetting->endtimehash;  
           $urlss = "https://world-meds.herokuapp.com/?task=confirm&hash=".$endtimehash;
                $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL,$urlss);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $endtimehashstatus=curl_exec($cSession);
                curl_close($cSession);
            $statusthree = str_replace('"','',$endtimehashstatus,$count);     
           if ($statusthree=="Done") {
             $c=1;
           }else if($statusthree=="Panding"){
             $c=2;
           }else if($statusthree=="Not"){
             $c=3;
           }else{
             $c=4;
           }
           $finalizehash = $setcrowddetting->finalizehash;  
           $urlone = "https://world-meds.herokuapp.com/?task=confirm&hash=".$finalizehash;
                $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL,$urlone);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $finalizehashstatus=curl_exec($cSession);
                curl_close($cSession);
            $statusfour = str_replace('"','',$finalizehashstatus,$count);     
           if ($statusfour=="Done") {
             $d=1;
           }else if($statusfour=="Panding"){
             $d=2;
           }else if($statusfour=="Not"){
             $d=3;
           }else{
             $d=4;
           }
           $killcontracthash = $setcrowddetting->killcontracthash;  
           $urltwo = "https://world-meds.herokuapp.com/?task=confirm&hash=".$killcontracthash;
                $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL,$urltwo);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $killcontracthashstatus=curl_exec($cSession);
                curl_close($cSession);
            $statusfive = str_replace('"','',$killcontracthashstatus,$count);     
           if ($statusfive=="Done") {
             $e=1;
           }else if($statusfive=="Panding"){
             $e=2;
           }else if($statusfive=="Not"){
             $e=3;
           }else{
             $e=4;
           }
         
          
                  

       return view('admin.setcrowddetting.index', compact('startsAt','endsAt','TokenPerETHs','a','TokenPerETHshash','starttimehash','b','endtimehash','c','d','e','','finalizehash','killcontracthash'));

    }
    
    public function tokenpereth(Request $request){
       
      $crowdsellprice = abs($request->crowdsellprice);
     
      if($crowdsellprice!=0){
        $ico_setting = IcoSetting::first();
         $eth_address= $ico_setting->ETH_merchant_address;
         $ETH_merchant_private = $ico_setting->ETH_merchant_private;

         if($ETH_merchant_private==null){
           return back()->with('alert', 'Enter the Merchant Private Key!!!'); 
         }else{
           if($eth_address==null){
            return back()->with('alert', 'Enter the Merchant Address!!!'); 
           }else{
             $url = "https://world-meds.herokuapp.com/?task=setRate&Value=".$crowdsellprice."&FromAddress=".$eth_address."&PrivateKey=".$ETH_merchant_private;
             $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL, $url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $result=curl_exec($cSession);
                curl_close($cSession);
                 $result = str_replace('"','',$result,$count);
                 if ($count==2) {
                   $updte= IcoSetting::where('id', 1)->update(['TokenPerETH' => $crowdsellprice,'TokenPerETHhash'=>$result]);
                   return back()->with('success', ' Updated Successfully!');
                 }
           }
         }

      



      }else{
        return back()->with('alert', 'Enter the correct value!!!'); 
      }



    }

    public function starttime(Request $request){
      
        $starttime = abs($request->starttime);
     
      if($starttime!=0){
        $ico_setting = IcoSetting::first();
         $eth_address= $ico_setting->ETH_merchant_address;
         $ETH_merchant_private = $ico_setting->ETH_merchant_private;

         if($ETH_merchant_private==null){
           return back()->with('alert', 'Enter the Merchant Private Key!!!'); 
         }else{
           if($eth_address==null){
            return back()->with('alert', 'Enter the Merchant Address!!!'); 
           }else{
             $url = "https://world-meds.herokuapp.com/?task=setStartsAt&Value=".$starttime."&FromAddress=".$eth_address."&PrivateKey=".$ETH_merchant_private;
             $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL, $url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $result=curl_exec($cSession);
                curl_close($cSession);
                 $result = str_replace('"','',$result,$count);
                 if ($count==2) {
                   $updte= IcoSetting::where('id', 1)->update(['Start_time' => $starttime,'starttimehash'=>$result]);
                   return back()->with('success', ' Updated Successfully!');
                 }
           }
         }

    



      }else{
        return back()->with('alert', 'Enter the correct value!!!'); 
      }

    }
    public function endtime(Request $request){
      
      $endtime = abs($request->endtime);
     
      if($endtime!=0){
        $ico_setting = IcoSetting::first();
         $eth_address= $ico_setting->ETH_merchant_address;
         $ETH_merchant_private = $ico_setting->ETH_merchant_private;

         if($ETH_merchant_private==null){
           return back()->with('alert', 'Enter the Merchant Private Key!!!'); 
         }else{
           if($eth_address==null){
            return back()->with('alert', 'Enter the Merchant Address!!!'); 
           }else{
             $url = "https://world-meds.herokuapp.com/?task=setEndsAt&Value=".$endtime."&FromAddress=".$eth_address."&PrivateKey=".$ETH_merchant_private;
             $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL, $url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $result=curl_exec($cSession);
                curl_close($cSession);
                 $result = str_replace('"','',$result,$count);
                 if ($count==2) {
                   $updte= IcoSetting::where('id', 1)->update(['End_time' => $endtime,'endtimehash'=>$result]);
                   return back()->with('success', ' Updated Successfully!');
                 }
           }
         }

    



      }else{
        return back()->with('alert', 'Enter the correct value!!!'); 
      }

    }
    public function finalize(Request $request){
      
      $ico_setting = IcoSetting::first();
      $eth_address= $ico_setting->ETH_merchant_address;
      $ETH_merchant_private = $ico_setting->ETH_merchant_private; 
      
      if($ETH_merchant_private==null){
           return back()->with('alert', 'Enter the Merchant Private Key!!!'); 
         }else{
           if($eth_address==null){
            return back()->with('alert', 'Enter the Merchant Address!!!'); 
           }else{
             $url = "https://world-meds.herokuapp.com/?task=finalize&FromAddress=".$eth_address."&PrivateKey=".$ETH_merchant_private;
             $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL, $url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $result=curl_exec($cSession);
                curl_close($cSession);
                 $result = str_replace('"','',$result,$count);
                 if ($count==2) {
                   $updte= IcoSetting::where('id', 1)->update(['finalizehash'=>$result]);
                   return back()->with('success', ' Updated Successfully!');
                 }
           }
         }  

    }
    public function killcontract(Request $request){
      
      $ico_setting = IcoSetting::first();
      $eth_address= $ico_setting->ETH_merchant_address;
      $ETH_merchant_private = $ico_setting->ETH_merchant_private;
       if($ETH_merchant_private==null){
           return back()->with('alert', 'Enter the Merchant Private Key!!!'); 
         }else{
           if($eth_address==null){
            return back()->with('alert', 'Enter the Merchant Address!!!'); 
           }else{
             $url = "https://world-meds.herokuapp.com/?task=kill&FromAddress=".$eth_address."&PrivateKey=".$ETH_merchant_private;
             $cSession = curl_init(); 
                curl_setopt($cSession,CURLOPT_URL, $url);
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                $result=curl_exec($cSession);
                curl_close($cSession);
                 $result = str_replace('"','',$result,$count);
                 if ($count==2) {
                   $updte= IcoSetting::where('id', 1)->update(['killcontracthash'=>$result]);
                   return back()->with('success', ' Updated Successfully!');
                 }
           }
         } 
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gateway  $gateway
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gateway $gateway)
    {
        //
    }
}
