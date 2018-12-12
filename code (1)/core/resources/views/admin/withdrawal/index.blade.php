@extends('admin.layout.master')

@section('content')
<style>
/* Style tab links */
.tablink {
    background-color: #555;
    color: white;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    font-size: 17px;
    width: 25%;
}

.tablink:hover {
    background-color: #777;
}

/* Style the tab content (and add height:100% for full page content) */
.tabcontent {
    color: #000;
    display: none;
    padding: 100px 20px;
    height: 100%;
}

#Home {background-color: red;}
#News {background-color: green;}
#Contact {background-color: blue;}
#About {background-color: orange;}
form {
    float: left;
}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption font-red-sunglo">
					<i class="icon-settings font-red-sunglo"></i>
					<span class="caption-subject bold uppercase">Withdrawal Request Settings</span>

				</div> 
			</div>
<button class="tablink" onclick="openPage('ETH_WITHDRAWAl', this, 'red')" id="defaultOpen">ETH Withdrawal</button>
<button class="tablink" onclick="openPage('BTC_WITHDRAWAl', this, 'blue')" id="defaultOpen">BTC Withdrawal</button>
<button class="tablink" onclick="openPage('LTC_WITHDRAWAl', this, 'black')" id="defaultOpen">LTC Withdrawal</button>
<button class="tablink" onclick="openPage('Wdmd_WITHDRAWAl', this, 'green')" >Wdmd Withdrawal</button>

<div id="ETH_WITHDRAWAl" class="tabcontent">
@if(session()->has('message'))
    <div class="alert alert-success">
        <a target='_blank' href='https://ropsten.etherscan.io/tx/<?php echo session()->get('message');?>'>{{ session()->get('message') }}</a>
    </div>
@endif
   <h2>ETH </h2>
   <div class="container">
   <h2>ETH Verification Detail</h2>
    <div class="merchant_eth_balance">
    ETH Balance: <?php echo $Ether; ?>
    </div>
    <div class="tableone">
   <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover order-column">
                <thead>
                    <tr>
                        <th>
                            User ID 
                        </th>
                        <th>
                            Task
                        </th>
                        <th>
                            Item
                        </th>
                        <th>
                             Amount
                        </th>                       
                        <th>
                            Amount-By
                        </th>
                        <th>
                           Hash
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Action
                        </th>
                     </tr>
                </thead>
                <tbody>
                    <?php $count=1;
       foreach($fail_data as $fail_datas){
        ?> 
        <tr>
                            <td><?php echo $fail_datas->user_id;?></td>
                           <td><?php echo $fail_datas->task;?></td> 
                           <td><?php echo $fail_datas->item;?></td>
                           <td><?php echo $fail_datas->amount;?></td>
                           <td><?php echo $fail_datas->amount_by;?></td>
                            <form method="post" autocomplete="off" action="{{route('withdrawal_request.faileth')}}">
                                {{ csrf_field() }}
                           <td><input type="text" name="hash" value="<?php echo $fail_datas->hash;?>"></td>
                           <td><input type="text" name="status" value="<?php echo $fail_datas->status;?>" ></td>
                               
                               <input type="hidden" name="id" value="<?php echo $fail_datas->id;?>">
                               <td><input type="submit" name="update_status_<?php echo $fail_datas->id;?>" value="Update"></td>
                               </form>
                         </tr>
                              <?php
     } 
     

                      if(sizeof($fail_data) < 1){
                            echo "<tr><td class='text-center' colspan='8'>No Failed Transaction Found</td></tr>"; 
                      }
                           ?>
                 
   
      <tbody>
           </table>
            <?php echo $fail_data->render(); ?>
          
        </div>
    </div>

  
    <div class="tabletwo">
    <div class="portlet-body">
              
                <table class="table table-striped table-bordered table-hover order-column">
                <thead>

                    <tr>
                        <th>
                            User ID 
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Address/Hash
                        </th>
                        <th>
                             Amount-Send
                        </th>                       
                        <th>
                            Fee
                        </th>
                        <th>
                           Total
                        </th>
                        <th>
                            Item
                        </th>
                        <th>
                            Action
                        </th>
                     </tr>
                </thead>
                <tbody>
                 <?php $count=1;
       foreach($eth_widthrawal_request as $eth_widthrawal_requests){
        ?> 
        
                     <tr>
                      <td><?php echo $count;?></td>
                      <td><?php echo $eth_widthrawal_requests->name;?></td>
                      <td><?php echo $eth_widthrawal_requests->address;?></td>
                      <td><?php echo $eth_widthrawal_requests->amount_send;?></td>
                      <td><?php echo $eth_widthrawal_requests->fee;?></td>
                      <td><?php echo $eth_widthrawal_requests->total;?></td>
                      <td><?php echo $eth_widthrawal_requests->item;?></td>
                     <td>
                      <form method="post" autocomplete="off" action="{{route('withdrawal_request.automatic')}}">
                      {{ csrf_field() }}
                      <input type="hidden" name="amount_send" value="<?php echo $eth_widthrawal_requests->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $eth_widthrawal_requests->address;?>">
                      <input type="hidden" name="id" value="<?php echo $eth_widthrawal_requests->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $eth_widthrawal_requests->user_id;?>">
                      <input type='submit' id='btn_hash_<?php echo $eth_widthrawal_requests->id;?><?php echo $eth_widthrawal_requests->user_id;?>' name='btn_hash_<?php echo $eth_widthrawal_requests->id;?><?php echo $eth_widthrawal_requests->user_id;?>' class='btn btn-success' value='Update'>
                      </form>
                      <span data-popup-open='popup-BTC' style='height: 33px;width: 66px;' data-toggle='modal' data-target='#reject<?php echo $eth_widthrawal_requests->id;?>'>
                      <input type='button' name='reject<?php echo $eth_widthrawal_requests->id;?>' class='btn btn-danger' value='Reject' id="reject<?php echo $eth_widthrawal_requests->id;?>" ></span>
                      </td>              
                    
                     </tr>
                     <?php $count++;?>
                      <div class="modal fade" id="reject<?php echo $eth_widthrawal_requests->id;?>" role="dialog" style="margin-top:10pc">
                    <div class="modal-dialog">
                       
                      <div class="modal-content">
                        <div class="modal-header">
                         Reject to Decline
                        </div>
                        <div class="modal-body">
                         <form role="form" method="POST" action="{{route('withdrawal_request.reject')}}">
                         {{ csrf_field() }}
                               <label for="comment">Reason:</label>
                               <input type="text" name="staus_hash" >
                               <label for="comment">Status</label>
                                <input type="text" name="status" value="Reject" readonly>
                                <input type="hidden" name="amount_send" value="<?php echo $eth_widthrawal_requests->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $eth_widthrawal_requests->address;?>">
                      <input type="hidden" name="id" value="<?php echo $eth_widthrawal_requests->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $eth_widthrawal_requests->user_id;?>">
                       <input type="hidden" name="total" value="<?php echo $eth_widthrawal_requests->total;?>">
                               <button type="submit" class="btn btn-success" name='reject<?php echo $eth_widthrawal_requests->id;?>' >Submit</button>
                          </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                    
                     <?php
     } 
     

                      if(sizeof($eth_widthrawal_request) < 1){
                            echo "<tr><td class='text-center' colspan='8'>No Failed Transaction Found</td></tr>"; 
                      }
                           ?>
                   
                 
      <tbody>
           </table>
            <?php echo $eth_widthrawal_request->render(); ?>
        </div>
    </div>
   </div>
  
</div>

<div id="BTC_WITHDRAWAl" class="tabcontent">
@if(session()->has('btchash'))
    <div class="alert alert-success">
        {{ session()->get('btchash') }}
    </div>
@endif
<h2>BTC</h2>
<div class="container">
<h2>BTC Verification Detail</h2>
<div class="merchant_eth_balance">
BTC Balance: <?php echo $btc_avail_balance; ?>
<br/>
Unconfirm BTC Balance: <?php echo $btc_pending_received_balance; ?>
</div>
<div class="tabletwo">
    <div class="portlet-body">
              
                <table class="table table-striped table-bordered table-hover order-column">
                <thead>

                    <tr>
                        <th>
                            User ID 
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Address/Hash
                        </th>
                        <th>
                             Amount-Send
                        </th>                       
                        <th>
                            Fee
                        </th>
                        <th>
                           Total
                        </th>
                        <th>
                            Item
                        </th>
                        <th>
                            Action
                        </th>
                     </tr>
                </thead>
                <tbody>
                 <?php $count=1;
       foreach($bth_widthrawal_request as $bth_widthrawal_requests){
        ?> 
        
                     <tr>
                      <td><?php echo $count;?></td>
                      <td><?php echo $bth_widthrawal_requests->name;?></td>
                      <td><?php echo $bth_widthrawal_requests->address;?></td>
                      <td><?php echo $bth_widthrawal_requests->amount_send;?></td>
                      <td><?php echo $bth_widthrawal_requests->fee;?></td>
                      <td><?php echo $bth_widthrawal_requests->total;?></td>
                      <td><?php echo $bth_widthrawal_requests->item;?></td>
                     <td>
                      <form method="post" autocomplete="off" action="{{route('withdrawal_request.btcautomatic')}}">
                      {{ csrf_field() }}
                      <input type="hidden" name="amount_send" value="<?php echo $bth_widthrawal_requests->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $bth_widthrawal_requests->address;?>">
                      <input type="hidden" name="id" value="<?php echo $bth_widthrawal_requests->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $bth_widthrawal_requests->user_id;?>">
                      <input type='submit' id='btn_hash_<?php echo $bth_widthrawal_requests->id;?><?php echo $bth_widthrawal_requests->user_id;?>' name='btn_hash_<?php echo $bth_widthrawal_requests->id;?><?php echo $bth_widthrawal_requests->user_id;?>' class='btn btn-success' value='Update'>
                      </form>
                      <span data-popup-open='popup-BTC' style='height: 33px;width: 66px;' data-toggle='modal' data-target='#reject<?php echo $bth_widthrawal_requests->id;?>'>
                      <input type='button' name='reject<?php echo $bth_widthrawal_requests->id;?>' class='btn btn-danger' value='Reject' id="reject<?php echo $bth_widthrawal_requests->id;?>" ></span>
                      </td>              
                    
                     </tr>
                     <?php $count++;?>
                      <div class="modal fade" id="reject<?php echo $bth_widthrawal_requests->id;?>" role="dialog" style="margin-top:10pc">
                    <div class="modal-dialog">
                       
                      <div class="modal-content">
                        <div class="modal-header">
                         Reject to Decline
                        </div>
                        <div class="modal-body">
                         <form role="form" method="POST" action="{{route('withdrawal_request.btcreject')}}">
                         {{ csrf_field() }}
                               <label for="comment">Reason:</label>
                               <input type="text" name="staus_hash" >
                               <label for="comment">Status</label>
                                <input type="text" name="status" value="Reject" readonly>
                                <input type="hidden" name="amount_send" value="<?php echo $bth_widthrawal_requests->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $bth_widthrawal_requests->address;?>">
                      <input type="hidden" name="id" value="<?php echo $bth_widthrawal_requests->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $bth_widthrawal_requests->user_id;?>">
                       <input type="hidden" name="total" value="<?php echo $bth_widthrawal_requests->total;?>">
                               <button type="submit" class="btn btn-success" name='reject<?php echo $bth_widthrawal_requests->id;?>' >Submit</button>
                          </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                    
                     <?php
     } 
     

                      if(sizeof($bth_widthrawal_request) < 1){
                            echo "<tr><td class='text-center' colspan='8'>No Failed Transaction Found</td></tr>"; 
                      }
                           ?>
                   
                 
      <tbody>
           </table>
            <?php echo $bth_widthrawal_request->render(); ?>
        </div>
    </div>
</div>

</div>
<div id="LTC_WITHDRAWAl" class="tabcontent">
@if(session()->has('ltchash'))
    <div class="alert alert-success">
        {{ session()->get('ltchash') }}
    </div>
@endif
<h2>LTC</h2>
<div class="container">
<h2>LTC Verification Detail</h2>
<div class="merchant_eth_balance">
LTC Balance: <?php echo $ltc_avail_balance; ?>
<br/>
Unconfirm LTC Balance: <?php echo $ltc_pending_received_balance; ?>
</div>
<div class="tabletwo">
    <div class="portlet-body">
              
                <table class="table table-striped table-bordered table-hover order-column">
                <thead>

                    <tr>
                        <th>
                            User ID 
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Address/Hash
                        </th>
                        <th>
                             Amount-Send
                        </th>                       
                        <th>
                            Fee
                        </th>
                        <th>
                           Total
                        </th>
                        <th>
                            Item
                        </th>
                        <th>
                            Action
                        </th>
                     </tr>
                </thead>
                <tbody>
                 <?php $count=1;
       foreach($lth_widthrawal_request as $lth_widthrawal_requests){
        ?> 
        
                     <tr>
                      <td><?php echo $count;?></td>
                      <td><?php echo $lth_widthrawal_requests->name;?></td>
                      <td><?php echo $lth_widthrawal_requests->address;?></td>
                      <td><?php echo $lth_widthrawal_requests->amount_send;?></td>
                      <td><?php echo $lth_widthrawal_requests->fee;?></td>
                      <td><?php echo $lth_widthrawal_requests->total;?></td>
                      <td><?php echo $lth_widthrawal_requests->item;?></td>
                     <td>
                      <form method="post" autocomplete="off" action="{{route('withdrawal_request.ltcautomatic')}}">
                      {{ csrf_field() }}
                      <input type="hidden" name="amount_send" value="<?php echo $lth_widthrawal_requests->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $lth_widthrawal_requests->address;?>">
                      <input type="hidden" name="id" value="<?php echo $lth_widthrawal_requests->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $lth_widthrawal_requests->user_id;?>">
                      <input type='submit' id='btn_hash_<?php echo $lth_widthrawal_requests->id;?><?php echo $lth_widthrawal_requests->user_id;?>' name='btn_hash_<?php echo $lth_widthrawal_requests->id;?><?php echo $lth_widthrawal_requests->user_id;?>' class='btn btn-success' value='Update'>
                      </form>
                      <span data-popup-open='popup-BTC' style='height: 33px;width: 66px;' data-toggle='modal' data-target='#reject<?php echo $lth_widthrawal_requests->id;?>'>
                      <input type='button' name='reject<?php echo $lth_widthrawal_requests->id;?>' class='btn btn-danger' value='Reject' id="reject<?php echo $lth_widthrawal_requests->id;?>" ></span>
                      </td>              
                    
                     </tr>
                     <?php $count++;?>
                      <div class="modal fade" id="reject<?php echo $lth_widthrawal_requests->id;?>" role="dialog" style="margin-top:10pc">
                    <div class="modal-dialog">
                       
                      <div class="modal-content">
                        <div class="modal-header">
                         Reject to Decline
                        </div>
                        <div class="modal-body">
                         <form role="form" method="POST" action="{{route('withdrawal_request.ltcreject')}}">
                         {{ csrf_field() }}
                               <label for="comment">Reason:</label>
                               <input type="text" name="staus_hash" >
                               <label for="comment">Status</label>
                                <input type="text" name="status" value="Reject" readonly>
                                <input type="hidden" name="amount_send" value="<?php echo $lth_widthrawal_requests->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $lth_widthrawal_requests->address;?>">
                      <input type="hidden" name="id" value="<?php echo $lth_widthrawal_requests->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $lth_widthrawal_requests->user_id;?>">
                       <input type="hidden" name="total" value="<?php echo $lth_widthrawal_requests->total;?>">
                               <button type="submit" class="btn btn-success" name='reject<?php echo $lth_widthrawal_requests->id;?>' >Submit</button>
                          </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                    
                     <?php
     } 
     

                      if(sizeof($lth_widthrawal_request) < 1){
                            echo "<tr><td class='text-center' colspan='8'>No Failed Transaction Found</td></tr>"; 
                      }
                           ?>
                   
                 
      <tbody>
           </table>
            <?php echo $lth_widthrawal_request->render(); ?>
        </div>
    </div>
</div>

</div>


<div id="Wdmd_WITHDRAWAl" class="tabcontent">
@if(session()->has('messagetoken'))
    <div class="alert alert-success">
        <a target='_blank' href='https://ropsten.etherscan.io/tx/<?php echo session()->get('messagetoken');?>'>{{ session()->get('messagetoken') }}</a>
    </div>
@endif
<h2>Wdmd </h2>
   <div class="container">
   <h2>Wdmd Verification Detail</h2>
    <div class="merchant_eth_balance">
    Wdmd Balance: <?php echo $Token; ?>
    </div>
    <div class="tableone">
    <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover order-column">
                <thead>
                    <tr>
                        <th>
                            User ID 
                        </th>
                        <th>
                            Task
                        </th>
                        <th>
                            Item
                        </th>
                        <th>
                             Amount
                        </th>                       
                        <th>
                            Amount-By
                        </th>
                        <th>
                           Hash
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Action
                        </th>
                     </tr>
                </thead>
                <tbody>
                 <?php $count=1;
       foreach($fail_datas as $fail_datastoken){
        ?> 
      
                     <tr>
                    <td><?php echo $fail_datastoken->user_id;?></td>
                           <td><?php echo $fail_datastoken->task;?></td> 
                           <td><?php echo $fail_datastoken->item;?></td>
                           <td><?php echo $fail_datastoken->amount;?></td>
                           <td><?php echo $fail_datastoken->amount_by;?></td>
                           <form method="post" autocomplete="off" action="{{route('withdrawal_request.failtoken')}}">
                                {{ csrf_field() }}
                           <td><input type="text" name="hash" value="<?php echo $fail_datastoken->hash;?>"></td>
                           <td><input type="text" name="status" value="<?php echo $fail_datastoken->status;?>" ></td>
                           <input type="hidden" name="id" value="<?php echo $fail_datastoken->id;?>">
                           <td><input type="submit" name="update_status_<?php echo $fail_datastoken->id;?>" value="Update"></td>
                          </form>
                     </tr>
                     <?php
     } 
     

                      if(sizeof($fail_datas) < 1){
                            echo "<tr><td class='text-center' colspan='8'>No Failed Transaction Found</td></tr>"; 
                      }
                           ?>
    
      <tbody>
           </table>
          <?php echo $fail_datas->render(); ?>
        </div>
    </div>

    
    <div class="tabletwo">
      <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover order-column">
                <thead>
                    <tr>
                        <th>
                            User ID 
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Address/Hash
                        </th>
                        <th>
                             Amount-Send
                        </th>                       
                        <th>
                            Fee
                        </th>
                        <th>
                           Total
                        </th>
                        <th>
                            Item
                        </th>
                        <th>
                            Action
                        </th>
                     </tr>
                </thead>
                <tbody>
        <?php $count=1;
       foreach($eth_widthrawal_request_token as $eth_widthrawal_request_tokens){
        ?> 
                     <tr>
                      <td><?php echo $count;?></td>
                      <td><?php echo $eth_widthrawal_request_tokens->name;?></td>
                      <td><?php echo $eth_widthrawal_request_tokens->address;?></td>
                      <td><?php echo $eth_widthrawal_request_tokens->amount_send;?></td>
                      <td><?php echo $eth_widthrawal_request_tokens->fee;?></td>
                      <td><?php echo $eth_widthrawal_request_tokens->total;?></td>
                      <td><?php echo $eth_widthrawal_request_tokens->item;?></td>
                       <td>
                      <form method="post" autocomplete="off" action="{{route('withdrawal_request.automatictoken')}}">
                      {{ csrf_field() }}
                      <input type="hidden" name="amount_send" value="<?php echo $eth_widthrawal_request_tokens->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $eth_widthrawal_request_tokens->address;?>">
                      <input type="hidden" name="id" value="<?php echo $eth_widthrawal_request_tokens->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $eth_widthrawal_request_tokens->user_id;?>">
                      <input type="hidden" name="total" value="<?php echo $eth_widthrawal_request_tokens->total;?>">
                      <input type='submit' id='btn_hash_<?php echo $eth_widthrawal_request_tokens->id;?><?php echo $eth_widthrawal_request_tokens->user_id;?>' name='btn_hash_<?php echo $eth_widthrawal_request_tokens->id;?><?php echo $eth_widthrawal_request_tokens->user_id;?>' class='btn btn-success' value='Update'>
                      </form>
                      <span data-popup-open='popup-BTC' style='height: 33px;width: 66px;' data-toggle='modal' data-target='#rejecttoken<?php echo $eth_widthrawal_request_tokens->id;?>'>
                      <input type='button' name='rejecttoken<?php echo $eth_widthrawal_request_tokens->id;?>' class='btn btn-danger' value='Reject' id="rejecttoken<?php echo $eth_widthrawal_request_tokens->id;?>" ></span>
                      </td>
   
                     </tr>
                      <?php $count++;?>
                      <div class="modal fade" id="rejecttoken<?php echo $eth_widthrawal_request_tokens->id;?>" role="dialog" style="margin-top:10pc">
                    <div class="modal-dialog">
                       
                      <div class="modal-content">
                        <div class="modal-header">
                         Reject to Decline
                        </div>
                        <div class="modal-body">
                         <form role="form" method="POST" action="{{route('withdrawal_request.rejecttoken')}}">
                         {{ csrf_field() }}
                               <label for="comment">Reason:</label>
                               <input type="text" name="staus_hash" >
                               <label for="comment">Status</label>
                                <input type="text" name="status" value="Reject" readonly>
                                <input type="hidden" name="amount_send" value="<?php echo $eth_widthrawal_request_tokens->amount_send;?>">
                      <input type="hidden" name="address" value="<?php echo $eth_widthrawal_request_tokens->address;?>">
                      <input type="hidden" name="id" value="<?php echo $eth_widthrawal_request_tokens->id;?>">
                      <input type="hidden" name="user_id" value="<?php echo $eth_widthrawal_request_tokens->user_id;?>">
                       <input type="hidden" name="total" value="<?php echo $eth_widthrawal_request_tokens->total;?>">
                               <button type="submit" class="btn btn-success" name='reject<?php echo $eth_widthrawal_request_tokens->id;?>' >Submit</button>
                          </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                       <?php
     } 
     

                      if(sizeof($eth_widthrawal_request_token) < 1){
                            echo "<tr><td class='text-center' colspan='8'>No Failed Transaction Found</td></tr>"; 
                      }
                           ?>
                   
                 
      <tbody>
           </table>
            <?php echo $eth_widthrawal_request_token->render(); ?>
 
    
          
        </div>
    </div>
   </div>
</div>

  </div>
  </div>
  </div>

<script>
function openPage(pageName,elmnt,color) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }
    document.getElementById(pageName).style.display = "block";
    elmnt.style.backgroundColor = color;

}
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>

@endsection