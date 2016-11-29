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
				</div>
			</div>
							
			<div class = "form-group col-xs-12">
				{!! Form::open(['url' => url('/shippinglabel_print'), 'method' => 'get']) !!}
					{!! Form::label('unique_order_id', 'Shipping Order #', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-md-4">
						{!! Form::text('unique_order_id', '', ['id' => 'unique_order_id', 'class' => "form-control", 'placeholder' => "Enter Shipping Order #"]) !!}
						Update Address # 
					</div>
					
					<div class = "col-md-1">
						<input type="submit" class="btn btn-primary btn-block" name="pull" id="pull" alt="Pull" value="Pull" />	
					</div>
				{!! Form::close() !!}
			</div>
			
			<div class = "form-group col-xs-12">
				{!! Form::label('mail_class', 'Mail class', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('mail_class', null, ['id' => 'mail_class', 'class' => "form-control", 'placeholder' => "Enter Mail class"]) !!}
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
					{!! Form::text('tracking_number', null, ['id' => 'tracking_number', 'class' => "form-control", 'placeholder' => "Enter Tracking #"]) !!}
				</div>
				
			</div>	
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('name', 'Name', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('name',null, ['id' => 'name', 'class' => "form-control", 'placeholder' => "Enter Name"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('company', 'Company', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('company', null, ['id' => 'company', 'class' => "form-control", 'placeholder' => "Enter Company"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('address1', 'Address 1', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('address1', null, ['id' => 'address1', 'class' => "form-control", 'placeholder' => "Enter Address 1"]) !!}
				</div>
			</div>	
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('address2', 'Address 2', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('address2', null, ['id' => 'address2', 'class' => "form-control", 'placeholder' => "Enter Address 2"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('city', 'City', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('city', null, ['id' => 'city', 'class' => "form-control", 'placeholder' => "Enter City"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('state_city', 'State', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('state_city', null, ['id' => 'state_city', 'class' => "form-control", 'placeholder' => "Enter State"]) !!}
				</div>
			</div>	
			
			
			<div class = "form-group col-xs-12">
				{!! Form::label('postal_code', 'Postal code', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('postal_code', null, ['id' => 'postal_code', 'class' => "form-control", 'placeholder' => "Enter Postal code"]) !!}
				</div>
			</div>	
			
			<div class = "form-group col-xs-12">
				{!! Form::label('country', 'Country', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('country', null, ['id' => 'country', 'class' => "form-control", 'placeholder' => "Enter Country"]) !!}
				</div>
			</div>																																		
									
			
			<div class = "form-group col-xs-12">
				{!! Form::label('email', 'Email', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('email', null, ['id' => 'email', 'class' => "form-control", 'placeholder' => "Enter Email"]) !!}
				</div>
			</div>	

			<div class = "form-group col-xs-12">
				{!! Form::label('phone', 'Phone', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-md-4">
					{!! Form::text('phone', null, ['id' => 'phone', 'class' => "form-control", 'placeholder' => "Enter Phone"]) !!}
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
						<img style="width:175mm; height: auto; overflow: hidden;"  src="data:image/gif;base64,{{ $graphicImage }} "/>
					</div>	
				{!! Form::close() !!}
			</div>
			@endif
		</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">		
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

		$("#pull").on('click', function (event){

			unique_order_id = $('#unique_order_id').val();
			token = $('input[name=_token]').val();
			//console.log(token);
			route = '/shipping_lbl_print';

			$.ajax({
				  url: route,
				  headers: {'X-CSRF-TOKEN': token},
				  type: 'POST',
				  cache: false,
				  dataType: 'json',
				  data: {unique_order_id : unique_order_id},
				  success:function(response) {
					//console.log(response);
					$('#unique_order_id').val(response.unique_order_id);
					$('#mail_class').val(response.mail_class);
					$('#tracking_number').val(response.tracking_number);
					$('#name').val(response.name);
					$('#company').val(response.company);
					$('#address1').val(response.address1);
					$('#address2').val(response.address2);
					$('#city').val(response.city);
					$('#state_city').val(response.state_city);
					$('#postal_code').val(response.postal_code);
					$('#country').val(response.country);
					$('#email').val(response.email);
					$('#phone').val(response.phone);
					$('#counterWeight').val(response.counterWeight);
					$('#unique_order_id').focus();
			    }
			 });
			return false;
			
		});
		
	</script>		
</body>
</html>