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
    height: 350px;
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


</style>
<div class="row">
    <div class="col-md-12">
       <div class="panel panel-inverse">
                <div class="panel-heading">
                   <h4 class="panel-title" style="text-align:left;">WALLET</h4>
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
                      <h2 class="walletcoin"><?php echo $btc_balance; ?></h2>
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
                      <h2 class="walletcoin"><?php echo $ltc_balance ; ?></h2>
                    </div>
                  </div>
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
          </div>




          <div class="row mt wallet_row">
             <div class="col-lg-3 wallet_fex marginTTop">
               <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>Send {{$gnl->cur}}</h5>
                  </div>
     
              <form role="form" class="form-horizontal style-form" method="post" autocomplete="off" action="{{ route('token_submit') }}">
              {{ csrf_field() }}
                <div class="form-group has-success">
                   <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="token_balance" value="<?php echo $token_balance; ?>" >
                    <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="user_id" value="<?php echo $user_ID; ?>" >
                  <div class="col-lg-12">
                    <input type="text" placeholder="TO ADDRESS:"   class="form-control" name="token_address" required >
                 
                  </div>
            </div>
                <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="text" placeholder="AMOUNT IN {{$gnl->cur}} (ALL:0.0000 {{$gnl->cur}} )" autocomplete="off"  value="" class="form-control" name="token_amount" required >
                  </div>
                </div>
           <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="password" placeholder="PASSWORD"  value="" class="form-control" autocomplete="off"  name="token_password" required >
                  </div>
                </div>
                   <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="submit" name="token_submit">Withdraw From {{$gnl->cur}} Wallet</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- /form-panel -->
          </div>  
              
          <div class="col-lg-3 wallet_fex">
               <div class="grey-panel pn donut-chart wallet_fex1">
                  <div class="grey-header">
                    <h5>Send Ethereum (ETH)</h5>
                  </div>
     
              <form role="form" class="form-horizontal style-form" method="post" autocomplete="off" action="{{ route('eth_submit') }}">
              {{ csrf_field() }}
                <div class="form-group has-success">
                     <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="eth_balance" value="<?php echo $eth_balance; ?>" >
                     <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="fee" value="<?php echo $fee; ?>" >
                  <div class="col-lg-12">
                    <input type="text" value=""  placeholder="TO ADDRESS:"  class="form-control" name="eth_address" required>
                 
                  </div>
            </div>
                <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="text" value="" inputmode="text"  placeholder="AMOUNT IN ETHEREUM (ALL:0.0000ETH )"  class="form-control" name="eth_amount" required> 
                  </div>
                </div>
           <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="password" value="" inputmode="text"  placeholder="PASSWORD"  class="form-control" name="eth_password" required >
                  </div>
                </div>
                   <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="submit" name="eth_submit">Withdraw From ETH Wallet</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- /form-panel -->
          </div>
          <div class="col-lg-3 wallet_fex">
               <div class="grey-panel pn donut-chart wallet_fex1">
                  <div class="grey-header">
                    <h5>Send Bitcoin (BTC)</h5>
                  </div>
     
              <form role="form" class="form-horizontal style-form" method="post" autocomplete="off" action="{{ route('btc_submit') }}">
              {{ csrf_field() }}
                <div class="form-group has-success">
                     <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="btc_balance" value="<?php echo $btc_balance; ?>" >
                     <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="fee" value="<?php echo $btcfee; ?>" >
                  <div class="col-lg-12">
                    <input type="text" value=""  placeholder="TO ADDRESS:"  class="form-control" name="btc_address" required>
                 
                  </div>
            </div>
                <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="text" value="" inputmode="text"  placeholder="AMOUNT IN Bitcoin (ALL:0.0000BTC)"  class="form-control" name="btc_amount" required> 
                  </div>
                </div>
           <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="password" value="" inputmode="text"  placeholder="PASSWORD"  class="form-control" name="btc_password" required >
                  </div>
                </div>
                   <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="submit" name="eth_submit">Withdraw From BTC Wallet</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- /form-panel -->
          </div>
          <div class="col-lg-3 wallet_fex">
               <div class="grey-panel pn donut-chart wallet_fex1">
                  <div class="grey-header">
                    <h5>Send Litecoin (LTC)</h5>
                  </div>
     
              <form role="form" class="form-horizontal style-form" method="post" autocomplete="off" action="{{ route('ltc_submit') }}">
              {{ csrf_field() }}
                <div class="form-group has-success">
                     <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="ltc_balance" value="<?php echo $ltc_balance; ?>" >
                     <input type="hidden" placeholder="TO ADDRESS:"   class="form-control" name="fee" value="<?php echo $ltcfee; ?>" >
                  <div class="col-lg-12">
                    <input type="text" value=""  placeholder="TO ADDRESS:"  class="form-control" name="ltc_address" required>
                 
                  </div>
            </div>
                <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="text" value="" inputmode="text"  placeholder="AMOUNT IN Litecoin (ALL:0.0000LTC )"  class="form-control" name="ltc_amount" required> 
                  </div>
                </div>
           <div class="form-group has-error">
                  <div class="col-lg-12">
                    <input type="password" value="" inputmode="text"  placeholder="PASSWORD"  class="form-control" name="ltc_password" required >
                  </div>
                </div>
                   <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="submit" name="eth_submit">Withdraw From LTC Wallet</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- /form-panel -->
          </div>
          
          <!-- /col-lg-12 -->
        </div>

        <div class="row mt margintop wallet_row">
              <!-- SERVER STATUS PANELS -->
              <div class="col-md-12 col-sm-12 mn2 ">
                <div class="grey-panel  deposit_section">
                 
                  <div class="row">
                    <div class="col-sm-4 col-xs-4">
                      <h2 class="walleth2">Deposit Ethereum (ETH)</h2>
                      <P>GET ADDRESS TO ACCEPT ETHEREUM PAYMENTS</P>
                        <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="button" data-toggle="modal" data-target="#eth_window"> Deposit Ethereum (ETH)</button>
                  </div>
                </div>
               
                
                
                
                    </div>
                     <div class="col-sm-4 col-xs-4">
                      <h2 class="walleth2">Deposit Bitcoin (BTC)</h2>
                      <P>GET ADDRESS TO ACCEPT Bitcoin PAYMENTS</P>
                        <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="button" data-toggle="modal" data-target="#btc_window"> Deposit BITCOIN (BTC)</button>
                  </div>
                </div>
               
                
                
                
                    </div>
                     <div class="col-sm-4 col-xs-4">
                      <h2 class="walleth2">Deposit Litecoin (LTC)</h2>
                      <P>GET ADDRESS TO ACCEPT Litecoin PAYMENTS</P>
                        <div class="form-group">
                  <div class="col-lg-12">
                    <button class="btn btn-theme btn-theme1" type="button" data-toggle="modal" data-target="#ltc_window"> Deposit LITECOIN (LTC)</button>
                  </div>
                </div>
               
                
                
                
                    </div>
                    
                    
                  </div>
                  
                  
                </div>
               
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
               
              <!-- /col-md-4-->
          </div>
          
            
              
              
                </div>


                <div class="content-panel new-content-panel">
              <section id="unseen">

               <h2 class="walleth2s">Deposit History</h2>
                <table class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                                                        <th class="numeric">S.NO</th>  
                                                        <th class="numeric">Hash</th>
                                                        <th class="numeric">Status</th>
                                                        <th class="numeric">Amount</th>
                                                        <th class="numeric">Coin Name</th>
                                                        <th class="numeric">Time </th>  
                              
        
                    </tr>
                  </thead>
                  <tbody>
                    
                    <tr>
                       <?php $count=1; ?>
      @foreach($history as $ref)
                                        <tr>
                                          <td class="numeric"><?php echo $count;?></td>
                                          <td class="numeric"><?php echo $ref->hash;?></td>
                                          <td class="numeric"><?php echo $ref->status;?></td>
                                          <?php if($ref->coin_name=="BTC"){?>
                                            <td class="numeric"><?php echo $ref->amount;?></td>
                                          <?php }elseif($ref->coin_name=="LTC"){?>
                                            <td class="numeric"><?php echo $ref->amount;?></td>
                                          <?php }else{?>
                                            <td class="numeric"><?php echo $ref->amount / pow(10,18);?></td>
                                          <?php }?>
                                          
                                          <td class="numeric"><?php echo $ref->coin_name;?></td>
                                          <td class="numeric"><?php echo $ref->time;?></td>
                                        </tr>
                                          <?php $count++;?>
      @endforeach
                     
                  
            
                       </tbody>
                </table>
<?php echo $history->render(); ?>

              </section>
            </div>

            <div class="row mt margintop wallet_row">
              <!-- SERVER STATUS PANELS -->
              <div class="col-md-12 col-sm-12 mn2 ">
                <div class="grey-panel pn donut-chart tablewallet">
                 
                  <div class="row">
                    <div class="col-sm-12 col-xs-12">
                      <h2 class="walleth2">Withdraw Requests</h2>
                        <div class="content-panel">
              <section id="unseen">
                            
                 
                   
                <table class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                                                        <th  class="numeric">S.No</th>
                                                        <th class="numeric">Address</th>  
                                                        <th class="numeric">Fee</th>  
                                                        <th class="numeric">Amount-Send</th>
                                                        <th class="numeric">Total</th>
                                                        <th class="numeric">Item</th>
                                                        <th class="numeric">Time </th>  
                    
        
                    </tr>
                  </thead>
                
                                                <?php $i=1;?>
                                                <?php foreach($withdrawal_request as $val){?> 
                                        <tr>
                                          <td class="numeric"><?php echo "$i";?></td>
                                          <td class="numeric"><?php echo $val->address;?></td>
                                          <td class="numeric"><?php echo $val->fee;?></td>
                                          <td class="numeric"><?php echo $val->amount_send;?></td>
                                          <td class="numeric"><?php echo $val->total;?></td>
                                          <td class="numeric"><?php echo $val->item;?></td>
                                          <td class="numeric"><?php echo $val->time;?></td>
                                        </tr>
                                          <?php $i=$i+1;?>
                                          <?php }?>
                    
                      
                </table>
                <?php echo $withdrawal_request->render(); ?>
              </section>
            </div>
                <!-- /grey-panel -->
              </div>
                    </div>
                    
                    
                  </div>
                  
                  
                </div>
              
              <!-- /col-md-4-->
               
              <!-- /col-md-4-->
          </div>
             
            
     
       
        
        <!-- /row -->
      </section>
    </section>
            </div>
        </div>
    </div>
</div>



<div class="modal deposit_window" id="eth_window" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Ethereum Deposit Address</h4>
        </div>
        <div class="modal-body">
          <p>Copy Ethereum Address And Scan QR Code To Proceed</p>
          
          <div class="qr_code">
              <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php  echo $eth_user_address; ?>" />
          </div><!--qr_code-->
          <div class="address">
             <?php echo  $eth_user_address; ?>
               
          </div><!--address-->
          
        </div>
        
      
      </div>
      
    </div>
  </div>

  <div class="modal deposit_window" id="btc_window" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Bitcoin Deposit Address</h4>
        </div>
        <div class="modal-body">
          <p>Copy Bitcoin Address And Scan QR Code To Proceed</p>
          
          <div class="qr_code">
              <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php  echo $btc_user_address; ?>" />
          </div><!--qr_code-->
          <div class="address">
             <?php echo  $btc_user_address; ?>
               
          </div><!--address-->
          
        </div>
        
      
      </div>
      
    </div>
  </div>
  <div class="modal deposit_window" id="ltc_window" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Litecoin Deposit Address</h4>
        </div>
        <div class="modal-body">
          <p>Copy Litecoin Address And Scan QR Code To Proceed</p>
          
          <div class="qr_code">
              <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php  echo $ltc_user_address; ?>" />
          </div><!--qr_code-->
          <div class="address">
             <?php echo  $ltc_user_address; ?>
               
          </div><!--address-->
          
        </div>
        
      
      </div>
      
    </div>
  </div>

@endsection