<?php

namespace App\Providers;

use App\General;
use App\Ico;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Auth;
use App\IcoSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

       $gnl = General::first();
        if($gnl == null)
        {
            $default = [
                'title' => 'THESOFTKING',
                'subtitle' => 'Subtitle',
                'startdate' => '2017-12-29',
                'color' => '009933',
                'cur' => 'BDT',
                'cursym' => 'TK',
                'decimal' => '2',
                'reg' => '1',
                'emailver' => '0',
                'smsver' => '1',
                'emailnotf' => '0',
                'smsnotf' => '1'
            ];
            General::create($default);
            $gnl = General::first();
        }
        view()->share('gnl',  $gnl);
        $url = "https://world-meds.herokuapp.com/?task=TokenPerETH";
        $cSession = curl_init(); 
        curl_setopt($cSession,CURLOPT_URL, $url);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cSession,CURLOPT_HEADER, false); 
        $TokenPerETH=curl_exec($cSession);
        curl_close($cSession);
        $ico_setting = IcoSetting::first();
        $TokenPerBTC=$ico_setting->TokenPerBTC;
        $TokenPerLTC=$ico_setting->TokenPerLTC;

        $data = array(
        'TokenPerETH' => $TokenPerETH,
        'TokenPerBTC' => $TokenPerBTC,
        'TokenPerLTC' => $TokenPerLTC,
);

        
      
        view()->share('data',  $data);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
