@extends('admin.layout.master')

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption font-red-sunglo">
					<i class="icon-settings font-red-sunglo"></i>
					<span class="caption-subject bold uppercase">Crowd Settings</span>
				</div>
			</div>
            <div class="container">
            <h2>Set Crowd-sell Start-time</h2>
           <form method="POST" action="{{route('setcrowddetting.starttime')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="set_starttime">Crowd-sell Start-time:</label>
                    <br/>
                    <span>(Enter the Date and time in unixstamp Format)</span>
                    <input type="text" class="form-control" id="set_starttime"  placeholder="Enter Crowd-sell Start-time-time"  name="starttime" value="{{$startsAt}}" >
                </div>
                <?php if($b==1){?>
               <button type="submit" name="set_starttime" class="btn btn-default">Set Start-time</button>
                <?php }else if($b==2){ ?>
                	<span class="buyspan">Transaction is Procress <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $starttimehash;?> "><?php echo $starttimehash;?></a></span>
                	<span>After Transaction Suceesfully value will change</span> 
                <?php }else if($b==3){ ?>
                	<span class="buyspan">Transaction is failed <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $starttimehash;?> "><?php echo $starttimehash;?></a></span>
                	<button type="submit" name="set_starttime" class="btn btn-default">Set Start-time</button>
                <?php }else{?>
                 <button type="submit" name="set_starttime" class="btn btn-default">Set Start-time</button>
                 <?php }?>
                
            </form>
            <h3></h3>
               <div class="container">
            <h2>Set Crowd-sell End-time</h2>
          <form method="POST" action="{{route('setcrowddetting.endtime')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="set_endtime">Crowd-sell End-time:</label>
                    <br/>
                    <span>(Enter the Date and time in unixstamp Format)</span>
                    <input type="text" class="form-control" id="set_endtime"  placeholder="Enter Crowd-sell End-time"  name="endtime" value="{{$endsAt}}" >
                </div>
                 <?php if($c==1){?>
               <button type="submit" name="set_endtime" class="btn btn-default">Set End-time</button>
                <?php }else if($c==2){ ?>
                	<span class="buyspan">Transaction is Procress <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $endtimehash;?> "><?php echo $endtimehash;?></a></span>
                	<span>After Transaction Suceesfully value will change</span> 
                <?php }else if($c==3){ ?>
                	<span class="buyspan">Transaction is failed <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $endtimehash;?> "><?php echo $endtimehash;?></a></span>
                	<button type="submit" name="set_endtime" class="btn btn-default">Set End-time</button>
                <?php }else{?>
                 <button type="submit" name="set_endtime" class="btn btn-default">Set End-time</button>
                 <?php }?>
               
            </form>
            <h3></h3>
        </div>
         </div>
        <div class="container">
            <h2>Set Crowd-sell Price</h2>
            <form method="POST" action="{{route('setcrowddetting.tokenpereth')}}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="crowdsell-price">Crowd-sell Price: </label>
                    <input type="text" class="form-control" id="crowdsell-price" placeholder="Enter Crowd-sell Price" name="crowdsellprice" value="{{$TokenPerETH}}">
                </div>
                <?php if($a==1){?>
                <button type="submit" name="crowdsell-price" class="btn btn-default">Crowdsell Price</button>
                <?php }else if($a==2){ ?>
                	<span class="buyspan">Transaction is Procress <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $TokenPerETHshash;?> "><?php echo $TokenPerETHshash;?></a></span>
                	<span>After Transaction Suceesfully value will change</span> 
                <?php }else if($a==3){ ?>
                	<span class="buyspan">Transaction is failed <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $TokenPerETHshash;?> "><?php echo $TokenPerETHshash;?></a></span>
                	<button type="submit" name="crowdsell-price" class="btn btn-default">Crowdsell Price</button>
                <?php }else{?>
                 <button type="submit" name="crowdsell-price" class="btn btn-default">Crowdsell Price</button>
                 <?php }?>
                
            </form>
            <h3></h3>
        </div>
        <div class="container">
            <h2>Finalize</h2>
           
            <form method="POST" action="{{route('setcrowddetting.finalize')}}">
            {{ csrf_field() }}
                <span>Note: if you click on this button then ico smart contract will  crowd-sell finalize<span>
                <br/>

                 <?php if($d==1){?>
                 <button type="submit" name="finalize" class="btn btn-danger">Finalize</button>
                <?php }else if($d==2){ ?>
                    <span class="buyspan">Transaction is Procress <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $finalizehash;?> "><?php echo $finalizehash;?></a></span>
                    <span>After Transaction Suceesfully value will change</span> 
                <?php }else if($d==3){ ?>
                    <span class="buyspan">Transaction is failed <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $finalizehash;?> "><?php echo $finalizehash;?></a></span>
                    <button type="submit" name="finalize" class="btn btn-danger">Finalize</button>
                <?php }else{?>
                  <button type="submit" name="finalize" class="btn btn-danger">Finalize</button>
                 <?php }?>

               
            </form>
            <h3></h3>
        </div>
        <div class="container">
            <h2>Kill Crowd-Sell Contact</h2>
            
          <form method="POST" action="{{route('setcrowddetting.killcontract')}}">
          {{ csrf_field() }}
                <span>Note: if you click on this button then ico smart contract will kill<span>
                <br/>
                 <?php if($e==1){?>
                 <button type="submit" name="kill_contact" class="btn btn-danger">Kill Contact</button>
                <?php }else if($e==2){ ?>
                    <span class="buyspan">Transaction is Procress <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $killcontracthash;?> "><?php echo $killcontracthash;?></a></span>
                    <span>After Transaction Suceesfully value will change</span> 
                <?php }else if($e==3){ ?>
                    <span class="buyspan">Transaction is failed <a target="_blank" href="https://ropsten.etherscan.io/tx/<?php echo $killcontracthash;?> "><?php echo $killcontracthash;?></a></span>
                   <button type="submit" name="kill_contact" class="btn btn-danger">Kill Contact</button>
                <?php }else{?>
                 <button type="submit" name="kill_contact" class="btn btn-danger">Kill Contact</button>
                 <?php }?>
                
            </form>
            <h3></h3>
        </div>
        </div>
        </div>
        </div>
@endsection