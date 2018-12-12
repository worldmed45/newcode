@extends('admin.layout.master')

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption font-red-sunglo">
					<i class="icon-settings font-red-sunglo"></i>
					<span class="caption-subject bold uppercase">ICO Settings</span>
				</div>
			</div>
			<div class="portlet-body form">
				<form role="form" method="POST" action="{{route('icosetting.update')}}">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-4">
							<h4>Hard CAP (Token)</h4>
							<input type="text" class="form-control input-lg" id="hard_cap" name="hard_cap" placeholder="Hard CAP" value="{{$ico_setting->Hard_cap}}">
						</div>
						<div class="col-md-4">
							<h4>Soft Cap (Token)</h4>
							<input type="text" class="form-control input-lg" id="soft_cap" name="soft_cap" placeholder="Soft Cap" value="{{$ico_setting->Soft_cap}}">
						</div>
						<div class="col-md-4">
							<h4>ICO Sale</h4>
							<input type="text" class="form-control input-lg" id="token_per_USD" name="IcoSaleText" placeholder="ICO sale" value="{{$ico_setting->IcoSaleText}}">
						</div>
						
					</div>

					

					<div class="row">
						<div class="col-md-4">
							<h4>ETH Raised</h4>
							<input type="text" class="form-control input-lg" id="eth_raised" name="eth_raised" placeholder="Eth Raised" value="{{$ico_setting->ETH_raised}}">
						</div>
						<div class="col-md-4">
							<h4>ETH Fees</h4>
							<input type="text" class="form-control input-lg" id="eth_fees" name="eth_fees" placeholder="Eth Fees" value="{{$ico_setting->eth_fees}}">
						</div>
						<div class="col-md-4">
							<h4>BTC Fees</h4>
							<input type="text" class="form-control input-lg" id="eth_fees" name="btc_fees" placeholder="Btc Fees" value="{{$ico_setting->btc_fees}}">
						</div>
						
						
						
					</div>

					<div class="row">
					<div class="col-md-4">
							<h4>LTC Fees</h4>
							<input type="text" class="form-control input-lg" id="eth_fees" name="ltc_fees" placeholder="Ltc Fees" value="{{$ico_setting->ltc_fees}}">
						</div>
						<div class="col-md-4">
							<h4>Token Sold</h4>
							<input type="text" class="form-control input-lg" id="token_sold" name="token_sold" placeholder="Token Sold" value="{{$ico_setting->Token_sold}}">
						</div>
						<div class="col-md-4">
							<h4>Token Per BTC Price </h4>
							<input type="text" class="form-control input-lg" id="Tokenperbtc" name="Tokenperbtc" placeholder="Token Per BTC " value="{{$ico_setting->TokenPerBTC}}">
						</div>
						<div class="col-md-4">
							<h4>Token Per LTC Price </h4>
							<input type="text" class="form-control input-lg" id="Tokenperltc" name="Tokenperltc" placeholder="Token Per LTC " value="{{$ico_setting->TokenPerLTC}}">
						</div>
						<div class="col-md-4">
							<h4>ETH merchant address</h4>
							<input type="text" class="form-control input-lg" id="ETH_merchant_address" name="ETH_merchant_address" placeholder="ETH merchant address" value="{{$ico_setting->ETH_merchant_address}}">
						</div>
						<div class="col-md-4">
							<h4>ETH Merchant Private</h4>
							<input type="text" class="form-control input-lg" id="ETH_merchant_private" name="ETH_merchant_private" placeholder="ETH Merchant Private" value="{{$ico_setting->ETH_merchant_private}}">
						</div>
						<div class="col-md-4">
							<h4>Referral Bonus %</h4>
							<input type="text" class="form-control input-lg" id="referral_bonus" name="referral_bonus" placeholder="Referral Bonus %" value="{{$ico_setting->referral_bonus}}">
						</div>
						
					</div>

					<div class="row">
					<div class="col-md-4">
							<h4>BTC Merchant Address</h4>
							<input type="text" class="form-control input-lg" id="BTC_merchant_address" name="BTC_merchant_address" placeholder="BTC Merchant Adrress" value="{{$ico_setting->BTC_merchant_address}}">
						</div>
				 <div class="col-md-4">
							<h4>LTC Merchant Address</h4>
							<input type="text" class="form-control input-lg" id="LTC_merchant_address" name="LTC_merchant_address" placeholder="LTC Merchant Adrress" value="{{$ico_setting->LTC_merchant_address}}">
						</div>
						<div class="col-md-4">
							<h4>BTC Merchant Api Key</h4>
							<input type="text" class="form-control input-lg" id="BTC_merchant_api_key" name="BTC_merchant_api_key" placeholder="BTC_merchant_api_key" value="{{$ico_setting->BTC_merchant_api_key}}">
						</div>
						<div class="col-md-4">
							<h4>LTC Merchant Api Key</h4>
							<input type="text" class="form-control input-lg" id="LTC_merchant_api_key" name="LTC_merchant_api_key" placeholder="LTC_merchant_api_key" value="{{$ico_setting->LTC_merchant_api_key}}">
						</div>
						<div class="col-md-4">
							<h4>BTC Secret Pin</h4>
							<input type="text" class="form-control input-lg" id="BTC_merchant_secret_pin" name="BTC_merchant_secret_pin" placeholder="BTC_merchant_secret_pin" value="{{$ico_setting->BTC_merchant_secret_pin}}">
						</div>
						<div class="col-md-4">
							<h4>LTC Secret Pin</h4>
							<input type="text" class="form-control input-lg" id="LTC_merchant_secret_pin" name="LTC_merchant_secret_pin" placeholder="LTC_merchant_secret_pin" value="{{$ico_setting->LTC_merchant_secret_pin}}">
						</div>
								
						
					</div>
					
					
					
				
					
					<div class="row">
						<hr/>
						<div class="col-md-6 col-md-offset-3">
							<button class="btn blue btn-block btn-lg">Update</button>
						</div>
					</div>
			</form>

			 
        </div>
		</div>
	</div>
</div>
</div>
@endsection
