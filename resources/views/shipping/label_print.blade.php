<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Shipping Label Print</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li>Shipping Label Print</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		<div class = "col-xs-12 text-right" style = "margin: 10px 0;">
@if(count($ship) > 0)		

			@if(count($errorMassage)>0)	
				<div class = "col-xs-12">
						<div class = "alert alert-warning text-center">
						@foreach($errorMassage as $errorMass)
							<h3>{{$errorMass}}</h3>
						@endforeach
						</div>
				</div>
			@endif	
			
			@if(count($ambiguousAdress)>0)	
				<div class = "col-xs-12">
					<div class = "text-left"><h3>Possible Suggest Address</h3></div>
				</div>
						
				@foreach($ambiguousAdress as $address)
				<div class = "col-xs-12">
				
					<div class = "text-left">
						<div>
							{{ $count++ }}) {{	$address['addressLine']	}}, {{ $address['region'] }}
							{{-- address1={{ $address['addressLine'] }}&city={{ $address['politicalDivision2'] }}&state_city={{ $address['politicalDivision1'] }}&postal_code={{ $address['postcodePrimaryLow'] }}&country={{ $address['countryCode'] }} --}}
							<a href = "{{ url(sprintf("/shipping_address_update?
								unique_order_id=%s
								&order_number=%s
								&address1=%s
								&city=%s
								&state_city=%s
								&postal_code=%s
								&country=%s", 
								$ship->unique_order_id, 
								$ship->order_number, 
								$address['addressLine'], 
								$address['politicalDivision2'], 
								$address['politicalDivision1'], 
								$address['postcodePrimaryLow'], 
								$address['countryCode'] 
								)) }}">Update
							</a>
						</div>
					</div>
				</div>							
				@endforeach
			@endif
				
			<div class = "form-group col-xs-12">
				<div class = "col-md-6">
					<a href = "{{ url(sprintf("/prints/shippinglable?
						unique_order_id=%s
						&order_number=%s
						&ship_address_1=%s
						&ship_address_2=%s
						&ship_state=%s
						&ship_city=%s
						&ship_zip=%s
						&ship_company_name=%s
						&ship_full_name=%s
						&ship_email=%s
						&ship_phone=%s
						&ship_country=%s", 
						$ship->unique_order_id,
						$ship->order_number,
						$ship->address1,
						$ship->address2,
						$ship->state_city,
						$ship->city,
						$ship->postal_code,
						$ship->company,
						$ship->name,
						$ship->email,
						$ship->phone,
						$ship->country
						)) }}"
					{{-- <a href = "#"  --}}
					   class = "btn btn-success btn-sm printShippingLabel @if((count($errorMassage)>0) || ($ship->tracking_number)) disabled @endif"
					   style = "font-size: 12px;">
					   Print Shipping Label
					</a>
				</div>
			</div>
							
			<div class = "form-group col-xs-12">
				{!! Form::open(['url' => url('/shippinglabel_print'), 'method' => 'get']) !!}
					{!! Form::label('unique_order_id', 'Shipping Order #', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-md-4">
						{!! Form::text('unique_order_id', $ship->unique_order_id, ['id' => 'unique_order_id', 'class' => "form-control", 'placeholder' => "Enter Shipping Order #"]) !!}
						Update Address # 
						<a href = "{{url(sprintf("orders/details/%s", $ship->order_number))}}"
													   target = "_blank">{{ $ship->order_number }}</a>
					</div>
					
					<div class = "col-md-1">
						<input type="submit" class="btn btn-primary btn-block" name="pull" id="pull" alt="Pull" value="Pull" />	
					</div>
				{!! Form::close() !!}
			</div>
			
			{!! Form::open(['url' => url('/shippinglabel_print'), 'method' => 'post']) !!}
			{!! Form::hidden('unique_order_id', $ship->unique_order_id, ['id' => 'unique_order_id']) !!}	
			{!! Form::hidden('order_number', $ship->order_number, ['id' => 'order_number']) !!}			
			<div class = "form-group col-xs-12">
				{!! Form::label('mail_class', 'Mail class', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('mail_class', $ship->mail_class, ['id' => 'mail_class', 'class' => "form-control", 'placeholder' => "Enter Mail class"]) !!}
				</div>
				{{--
				{!! Form::label('station_description', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('station_description', null, ['id' => 'station_description', 'class' => "form-control", 'placeholder' => "Enter station description"]) !!}
				</div>
				--}}
			</div>
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('tracking_number', 'Tracking #', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('tracking_number', $ship->tracking_number, ['id' => 'tracking_number', 'class' => "form-control", 'placeholder' => "Enter Tracking #"]) !!}
				</div>
				
				<div class = "col-md-1">
					@if(!$graphicImage)
						<a href = "{{ \Monogram\Helper::getTrackingUrl($ship->tracking_number) }}"
					   class = "btn btn-primary btn-sm @if((count($ambiguousAdress)>0) || (!$ship->tracking_number)) disabled @endif"
					   style = "font-size: 12px;">
					   View Delivery Status
						</a>
					@else
						<a href = "{{ \Monogram\Helper::getTrackingUrl($ship->tracking_number) }}"
					   class = "btn btn-primary btn-sm @if((count($ambiguousAdress)>0) || (!$ship->tracking_number)) disabled @endif"
					   style = "font-size: 12px;">
					   View Delivery Status
						</a>					
						
					@endif
				</div>
			</div>	
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('name', 'Name', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('name', $ship->customer->ship_full_name, ['id' => 'name', 'class' => "form-control", 'placeholder' => "Enter Name"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('company', 'Company', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('company', $ship->customer->ship_company_name, ['id' => 'company', 'class' => "form-control", 'placeholder' => "Enter Company"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('address1', 'Address 1', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('address1', $ship->customer->ship_address_1, ['id' => 'address1', 'class' => "form-control", 'placeholder' => "Enter Address 1"]) !!}
				</div>
			</div>	
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('address2', 'Address 2', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('address2', $ship->customer->ship_address_2, ['id' => 'address2', 'class' => "form-control", 'placeholder' => "Enter Address 2"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('city', 'City', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('city', $ship->customer->ship_city, ['id' => 'city', 'class' => "form-control", 'placeholder' => "Enter City"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('state_city', 'State', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('state_city', $ship->customer->ship_state, ['id' => 'state_city', 'class' => "form-control", 'placeholder' => "Enter State"]) !!}
				</div>
			</div>	
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('postal_code', 'Postal code', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('postal_code', $ship->customer->ship_zip, ['id' => 'postal_code', 'class' => "form-control", 'placeholder' => "Enter Postal code"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('country', 'Country', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('country', $ship->customer->ship_country, ['id' => 'country', 'class' => "form-control", 'placeholder' => "Enter Country"]) !!}
				</div>
			</div>																																		
									
			
			<div class = "form-group col-xs-12">
				{!! Form::label('email', 'Email', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('email', $ship->email, ['id' => 'email', 'class' => "form-control", 'placeholder' => "Enter Email"]) !!}
				</div>
			</div>	

			<div class = "form-group col-xs-12">
				{!! Form::label('phone', 'Phone', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('phone', $ship->phone, ['id' => 'phone', 'class' => "form-control", 'placeholder' => "Enter Phone"]) !!}
				</div>
			</div>	

			<div class = "form-group col-xs-12">
				{!! Form::label('counterWeight', 'Shipping Weight', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('counterWeight', $counterWeight, ['id' => 'counterWeight', 'class' => "form-control", 'placeholder' => "Enter Weight"]) !!}
				</div>
			</div>	
			
			
			{{--
			<div class = "col-xs-6 apply-margin-top-bottom">
				<div class = "col-xs-offset-2 col-xs-4">
					{!! Form::submit('Update Shipping Address',['class' => 'btn btn-primary btn-block']) !!}
				</div>
			</div>
			
			--}}
			{!! Form::close() !!}
			
			@if($graphicImage)	
			<div class = "form-group col-xs-12">
				{!! Form::open(['url' => url('/prints/shippinglabel_reprint'), 'method' => 'post']) !!}
					<div class = "col-md-2">
						<input type="submit" class="btn btn-primary btn-block" name="reprint" id="reprint" alt="Re Print" value="Re Print" />
					</div>
					<div class="current-batch" style="width:150mm; height: 100mm; border: none; ">
						{!! Form::hidden('graphicImage', $graphicImage, ['id' => 'graphicImage']) !!} 
						{{-- <img style="width:175mm; height: auto; overflow: hidden;"  src="data:image/gif;base64,{{ $graphicImage }} "/> --}}
						<img style="width:175mm; height: auto; overflow: hidden;"  src="{{ $graphicImage }}"/>
					</div>	
				{!! Form::close() !!}
			</div>
			@endif
		</div>
@else
		<div class = "form-group col-xs-12">
			{!! Form::open(['url' => url('/shippinglabel_print'), 'method' => 'get']) !!}
				{!! Form::label('unique_order_id', 'Shipping Order #', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('unique_order_id', null, ['id' => 'unique_order_id', 'class' => "form-control", 'placeholder' => "Enter Shipping Order #"]) !!}
				</div>
				
				<div class = "col-md-1">
					{!! Form::submit('Pull',['class' => 'btn btn-primary btn-block']) !!}
				</div>
				
				<div class = "col-xs-12">
					<div class = "alert alert-warning text-center">
						<h3>No Record found.</h3>
					</div>
				</div>
			
				
			{!! Form::close() !!}
		</div>
@endif			
	</div>

	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$(".printShippingLabel").on('click', function (event){
			$('#unique_order_id').val("");
			$('#mail_class').val("");
			$('#tracking_number').val("");
			$('#name').val("");
			$('#company').val("");
			$('#address1').val("");
			$('#address2').val("");
			$('#city').val("");
			$('#state_city').val("");
			$('#postal_code').val("");
			$('#country').val("");
			$('#email').val("");
			$('#phone').val("");
			$('#counterWeight').val("");
			$('#unique_order_id').focus();
// 			$(".printShippingLabel").addClass("disabled");
		});
	</script>		
</body>
</html>