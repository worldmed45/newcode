@extends('layouts.user')

@section('content')

<style>
.grey-panel {
    text-align: center;
    background: #2e6a9c;
    box-shadow: 0px 3px 2px #3b3170;
}
.pn {
    height: 200px;
    box-shadow: 0 2px 1px rgba(0, 0, 0, 0.2);
}
.grey-panel .grey-header {
    color: white;
    background: #00acac;
    padding: 3px;
    margin-bottom: 15px;
}
.grey-panel h5 {
    font-weight: 600;
    margin-top: 10px;
    font-size: 16px;
     color: white;
}
.logo {
    width: 50px;
    margin-top: 6px;
}
.goleft {
    text-align: left;
}
.donut-chart p {
    margin-top: 20px;
    font-weight: 700;
    margin-left: 15px;
    color: white;
}
.text-center {
    text-align: center;
}
#countdown {
    width: 465px;
    height: 160px;
    text-align: center;
    background: #2e6a9c;
    background-image: -webkit-linear-gradient(top, #2e6a9c, #2e6a9c, #2e6a9c, #e7e7e745);
    border: 1px solid #111;
    border-radius: 5px;
    box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.6);
    margin: 80px auto;
    padding: 24px 0;
    position: relative;
    top: 0;
    bottom: 0;
    left: 0px;
    right: 0;
}
#countdown #tiles {
    position: relative;
    z-index: 1;
}
#countdown .labels {
    width: 100%;
    height: 25px;
    text-align: center;
    position: absolute;
    bottom: 8px;
}
#countdown #tiles > span{
  width: 92px;
  max-width: 92px;
  font: bold 48px 'Droid Sans', Arial, sans-serif;
  text-align: center;
  color: #111;
  background-color: #ddd;
  background-image: -webkit-linear-gradient(top, #bbb, #eee); 
  background-image:    -moz-linear-gradient(top, #bbb, #eee);
  background-image:     -ms-linear-gradient(top, #bbb, #eee);
  background-image:      -o-linear-gradient(top, #bbb, #eee);
  border-top: 1px solid #fff;
  border-radius: 3px;
  box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.7);
  margin: 0 7px;
  padding: 18px 0;
  display: inline-block;
  position: relative;
}

#countdown #tiles > span:before{
  content:"";
  width: 100%;
  height: 13px;
  background: #111;
  display: block;
  padding: 0 3px;
  position: absolute;
  top: 41%; left: -3px;
  z-index: -1;
}

#countdown #tiles > span:after{
  content:"";
  width: 100%;
  height: 1px;
  background: #eee;
  border-top: 1px solid #333;
  display: block;
  position: absolute;
  top: 48%; left: 0;
}
#countdown .labels li {
    width: 102px;
    font: bold 15px 'Droid Sans', Arial, sans-serif;
    color: #FF6B6B;
    text-shadow: 1px 1px 0px #000;
    text-align: center;
    text-transform: uppercase;
    display: inline-block;
}
.navbar-brand>img {
    display: block;
    width: 41px;
}
</style>

<div class="row">
    <div class="col-md-12">
       <div class="panel panel-inverse">
                <div class="panel-heading">
                   <h4 class="panel-title" style="text-align:left;">Dashboard</h4>
                </div>
            <div class="panel-body">
        <section id="main-content" class="banner_section dashboard">
      <section class="wrapper">
        <div class="row dashboard_row">
          <div class="col-lg-12 main-chart">
            <!--CUSTOM CHART START -->
           
           
        
            <div class="row mt">
              <!-- SERVER STATUS PANELS -->
              <div class="col-md-3 col-sm-3 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>ETH-BALANCE</h5>
                  </div>
             
              <img class="logo" src="{{ asset('assets/images/logo/ethereum.png') }}">
                  <div class="row">
                    <div class="col-sm-4 col-xs-6 goleft">
                      <p>ETH<br>Balance:</p>
                    </div>
                    <div class="col-sm-8 col-xs-6">
                      <h3><?php echo $eth_balance; ?></h3>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
               <div class="col-md-3 col-sm-3 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>BTC-BALANCE</h5>
                  </div>
             
              <img class="logo" src="{{ asset('assets/images/logo/bitcoin.png') }}">
                  <div class="row">
                    <div class="col-sm-4 col-xs-6 goleft">
                      <p>BTC<br>Balance:</p>
                    </div>
                    <div class="col-sm-8 col-xs-6">
                      <h3><?php echo $btc_balance; ?></h3>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
               <div class="col-md-3 col-sm-3 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>LTC-BALANCE</h5>
                  </div>
             
              <img class="logo" src="{{ asset('assets/images/logo/litecoin.png') }}">
                  <div class="row">
                    <div class="col-sm-4 col-xs-6 goleft">
                      <p>LTC<br>Balance:</p>
                    </div>
                    <div class="col-sm-8 col-xs-6">
                      <h3><?php echo $ltc_balance; ?></h3>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
                <div class="col-md-3 col-sm-3 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>TOKEN-BALANCE</h5>
                  </div>
                
                <img class="logo" src="{{ asset('assets/images/logo/coin.png') }}">
                  <div class="row">
                    <div class="col-sm-4 col-xs-6 goleft">
                      <p>{{$gnl->cur}}<br>Balance:</p>
                    </div>
                    <div class="col-sm-8 col-xs-6">
                     <h3><?php echo $token_balance ;?></h3>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
             
              <!-- /col-md-4-->
              
              
                </div>
             
            
        <div class="sale_title text-center">
            <h1 style="color:#2e6a9c"><?php echo $ico_sale_text ;?> Sale <?php echo $text; ?></h1>
        </div>
        <div id="countdown">
            
  <div id='tiles'></div>
  <div class="labels">
    <li>Days</li>
    <li>Hours</li>
    <li>Mins</li>
    <li>Secs</li>
  </div>
</div>
        
        <!-- /row -->
      </section>
    </section>
            </div>
        </div>
    </div>
</div>
  <script>
        var target_date =  <?php echo $countdown;?>000;; // set the countdown date
var days, hours, minutes, seconds; // variables for time units

var countdown = document.getElementById("tiles"); // get tag element

getCountdown();

setInterval(function () { getCountdown(); }, 1000);

function getCountdown(){

  // find the amount of "seconds" between now and target
  var current_date = new Date().getTime();
  var seconds_left = (target_date - current_date) / 1000;

  days = pad( parseInt(seconds_left / 86400) );
  seconds_left = seconds_left % 86400;
     
  hours = pad( parseInt(seconds_left / 3600) );
  seconds_left = seconds_left % 3600;
      
  minutes = pad( parseInt(seconds_left / 60) );
  seconds = pad( parseInt( seconds_left % 60 ) );

  // format countdown string + set tag value
  countdown.innerHTML = "<span>" + days + "</span><span>" + hours + "</span><span>" + minutes + "</span><span>" + seconds + "</span>"; 
  
  var stats = "<?php echo $text ?>";
  if(stats==='is Expired'){
      countdown.innerHTML = "<span>" + '00' + "</span><span>" + '00' + "</span><span>" + '00' + "</span><span>" + '00' + "</span>"; 
  }
}

function pad(n) {
  return (n < 10 ? '0' : '') + n;
}


    </script>

@endsection
