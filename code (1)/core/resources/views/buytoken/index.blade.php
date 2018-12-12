@extends('layouts.user')

@section('content')
<style>
.grey-panel {
    text-align: center;
    background: #2e6a9c;
    box-shadow: 0px 3px 2px #2e6a9c;
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
    color:white;
}
.logo {
    width: 50px;
    margin-top: 6px;
}
.donut-chart p {
    margin-top: 35px;
    color: white;
    margin-left: 49px;
    font-weight: bold;
}
.goleft {
    text-align: left;
}
.donut-chart h2 {
    font-weight: 700;
    color: #fff;
    font-size: 30px;
}
.walletcoin {
    float: right;
    margin: 30px auto;
    padding-right: 10px;
}
.wallet_fex .grey-panel {
    height: 370px;
    box-shadow: 0px 3px 2px #2e6a9c;
}
.has-success .form-control {
    border-color: #ffffff3d;
    background: transparent;
    color: white;
}
.form-control {
    height: 45px;
}
.has-success .form-control {
    border-color: #ffffff3d;
    background: transparent;
    color: white;
}
.has-error .form-control {
    border-color: #ffffff3d;
    background: transparent;
    color: white;
}

.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.btn-theme1 {
    background: #00acac;
    margin: 30px auto;
    padding: 10px 30px;
    display: block;
    border-color: #00acac;
    color: white;
}
.form-group.has-success {
    margin-left: 10px;
    margin-right: 10px;
}
.form-group.has-error {
    margin-left: 10px;
    margin-right: 10px;
}
.deposit_section {
    margin-top: 2%;
        color: white;
}
.deposit_window {
    background: rgba(0,0,0,0.6);
}
.deposit_window .modal-content {
    z-index: 9999;
    background: #2e6a9c;
    color: #fff;
    max-width: 550px;
    text-align: center;
    padding: 20px 15px;
}
.deposit_window .modal-header {
    background: transparent;
}
.deposit_window .modal-header {
    position: relative;
    border: none;
}
.deposit_window .qr_code {
    max-width: 185px;
    margin: 26px auto;
}
.deposit_window .qr_code img {
    max-width: 100%;
}
.deposit_window .address {
    background: #fff;
    color: #232b42;
    padding: 12px 0px;
    /* border-radius: 35px; */
    font-size: 15px;
    font-weight: 600;
    outline: 5px #848ead inset;
    margin-top: 38px;
    margin-bottom: 8px;
}
.modal-title {
    margin: 0;
    line-height: 1.42857143;
    color: white;
}
h2.walleth2 {
    color: white;
}
.content-panel.new-content-panel {
    margin-top: 4%;
    background-color: #2e6a9c !important;
}
table.table.table-bordered.table-striped.table-condensed {
   
    color: #2e6a9c;
    border-color: #2e6a9c;
}
h2.walleth2s {
    color: white;
    text-align: center;
    font-weight: bold;
    padding: 8px 0px 0px 0px;
}
.marginTTop {
    margin-top: 5%;
    margin-bottom: 4%;
}
.buyspan {
    margin: 20px auto;
    text-align: center;
    display: block;
    padding: 10px;
    background: #07a7ff;
    width: 155px;
    border-radius: 30px;
    color: white;
}
option {
    color: #fff;
    background: #242a30;
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
                   <h4 class="panel-title" style="text-align:left;">BUY TOKEN</h4>
                </div>
            <div class="panel-body">
        <section id="main-content" class="banner_section dashboard">
      <section class="wrapper">
        <div class="row dashboard_row">
          <div class="col-lg-12 main-chart">
            <!--CUSTOM CHART START -->
           
           
        
            <div class="row mt">
              <!-- SERVER STATUS PANELS -->
              
              <div class="row mt wallet_row">
             
              <div class="col-md-3 col-sm-6 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>{{$gnl->cur}}-BALANCE</h5>
                  </div>
               <img class="logo" src="{{ asset('assets/images/logo/coin.png') }}" />
                  <div class="row">
                    <div class="col-sm-6 col-xs-6 goleft">
                      <p>{{$gnl->cur}} </p>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                      <h2  class="walletcoin"><?php echo number_format($token_balance,4) ; ?></h2>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
                <div class="col-md-3 col-sm-6 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>ETH-BALANCE</h5>
                  </div>
                 <img class="logo" src="{{ asset('assets/images/logo/ethereum.png') }}" />
                  <div class="row">
                    <div class="col-sm-6 col-xs-6 goleft">
                      <p>ETH </p>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                      <h2 class="walletcoin"><?php echo number_format($eth_balance,4) ; ?></h2>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
               <div class="col-md-3 col-sm-6 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>BTC-BALANCE</h5>
                  </div>
                 <img class="logo" src="{{ asset('assets/images/logo/bitcoin.png') }}" />
                  <div class="row">
                    <div class="col-sm-6 col-xs-6 goleft">
                      <p>BTC </p>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                      <h2 class="walletcoin"><?php echo number_format($btc_balance,4) ; ?></h2>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
               <!-- /col-md-4-->
                <div class="col-md-3 col-sm-6 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>LTC-BALANCE</h5>
                  </div>
                 <img class="logo" src="{{ asset('assets/images/logo/litecoin.png') }}" />
                  <div class="row">
                    <div class="col-sm-6 col-xs-6 goleft">
                      <p>LTC </p>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                      <h2 class="walletcoin"><?php echo number_format($ltc_balance,4) ; ?></h2>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
          </div>




          <div class="row mt wallet_row">
             <div class="col-lg-6 wallet_fex marginTTop col-lg-offset-3">
               <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>BUY TOKEN</h5>
                  </div>
     
              <form role="form" class="form-horizontal style-form" method="post" autocomplete="off" action="{{ route('buy_token') }}">
              {{ csrf_field() }}
                <div class="form-group has-success">
                 
                  <div class="col-lg-12">
                    <input type="text" placeholder="Amount"  id="ether"  class="form-control" name="NoEther" required >
                 
                  </div>
            </div>
             <div class="form-group has-success" >
                 
                  <div class="col-lg-12">
                  <select class="form-control" name="select_coin" id="coin_fee">
                   <option value="ETH">ETH</option>
                   <option value="BTC">BTC</option>
                  <option value="LTC">LTC</option>
 
                  </select>
                   
                 
                  </div>
            </div>
                <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="text" placeholder="TOKEN" autocomplete="off"  value="" class="form-control" id="token"  >
                  </div>
                </div>

                 <div class="buyspan">Network Fee : <div id="feestructure"><?php echo $fee; ?></div></div> 
           
                   <div class="form-group">
                  <div class="col-lg-12">
                  <?php   
                   
                    
                    if($text === 'Ends In'){
                    echo ' <button class="btn btn-theme btn-theme1" type="submit" name="buy_token">Buy Wdmd Token</button>' ;
                    }else{
                        echo "<p>Token Sale is Not available !</p>"; 
                    }
                  ?>
                   
                  </div>
                </div>
              </form>
            </div>
            <!-- /form-panel -->
          </div>  
            </div>
 </div>


             

           
          </div>
             
          
        <!-- /row -->
      </section>
    </section>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$('#coin_fee').on('change', function() {
  if(this.value=="ETH"){
   var ethfee = <?php echo $fee;?>;
   if (typeof <?php echo $TokenPerETHs; ?> !== 'undefined') { 
        $(document).ready(function(){
            //console.log('working...');
            $("#ether").keyup(function(){
                $("#token").val($("#ether").val()*<?php echo $TokenPerETHs; ?>);
            });
        });
        $(document).ready(function(){
            $("#token").keyup(function(){
                $("#ether").val($("#token").val()/<?php echo $TokenPerETHs; ?>);
            });
        });
    }
   $("#feestructure").html(ethfee);
  }else if(this.value=="BTC"){
  var btcfee = <?php echo $btcfee;?>;
  if (typeof <?php echo $TokenPerBTC; ?> !== 'undefined') { 
        $(document).ready(function(){
            //console.log('working...');
            $("#ether").keyup(function(){
                $("#token").val($("#ether").val()*<?php echo $TokenPerBTC; ?>);
            });
        });
        $(document).ready(function(){
            $("#token").keyup(function(){
                $("#ether").val($("#token").val()/<?php echo $TokenPerBTC; ?>);
            });
        });
    }
  $("#feestructure").html(btcfee);
  }else if(this.value=="LTC"){
   var ltcfee = <?php echo $ltcfee;?>;
   if (typeof <?php echo $TokenPerLTC; ?> !== 'undefined') { 
        $(document).ready(function(){
            //console.log('working...');
            $("#ether").keyup(function(){
                $("#token").val($("#ether").val()*<?php echo $TokenPerLTC; ?>);
            });
        });
        $(document).ready(function(){
            $("#token").keyup(function(){
                $("#ether").val($("#token").val()/<?php echo $TokenPerLTC; ?>);
            });
        });
    }
   $("#feestructure").html(ltcfee);
  }
});
</script>
<script>
    var coinname = $("#coin_fee option:selected").val()
    if(coinname=="ETH"){
    if (typeof <?php echo $TokenPerETHs; ?> !== 'undefined') { 
        $(document).ready(function(){
            //console.log('working...');
            $("#ether").keyup(function(){
                $("#token").val($("#ether").val()*<?php echo $TokenPerETHs; ?>);
            });
        });
        $(document).ready(function(){
            $("#token").keyup(function(){
                $("#ether").val($("#token").val()/<?php echo $TokenPerETHs; ?>);
            });
        });
    }
  }
 
</script>

<script>
$('#coin_fee').on('change', function() {
  if(this.value=="BTC"){
    var token= $("#token").val();
    var ether= $("#ether").val();
    $("#token").val($("#ether").val()*<?php echo $TokenPerBTC; ?>);
     $("#ether").val($("#token").val()/<?php echo $TokenPerBTC; ?>);
    
    }

     if(this.value=="LTC"){
    var token= $("#token").val();
    var ether= $("#ether").val();
    $("#token").val($("#ether").val()*<?php echo $TokenPerLTC; ?>);
     $("#ether").val($("#token").val()/<?php echo $TokenPerLTC; ?>);
    
    

  }
  if(this.value=="ETH"){
    var token= $("#token").val();
    var ether= $("#ether").val();
    $("#token").val($("#ether").val()*<?php echo $TokenPerETHs; ?>);
     $("#ether").val($("#token").val()/<?php echo $TokenPerETHs; ?>);
    
    

  }


});
</script>



@endsection
