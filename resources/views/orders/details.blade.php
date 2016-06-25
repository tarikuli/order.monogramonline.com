<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>View order details</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/nprogress.css">
	<style type = "text/css">
		body {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 10px;
			color: #000000;
		}

		table#items-table th {
			min-width: 100px;
		}

		table#items-table td {
			min-width: 100px;
			text-align: center;
		}

		table#items-table td img {
			max-width: 100px;
			max-height: 100px;
			float: left;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div style = "margin-left: 150px;margin-right: 150px">
		<ul style = "align: left;padding: 0px;margin-bottom: 5px">
			<li style = "display:inline"><a href = "{{url('/')}}">Home</a></li>
			<label>></label>
			<li style = "display:inline"><a href = "{{url('orders/list')}}">Orders</a></li>
			<label>></label>
			<li style = "display:inline" class = "active">Details</li>
		</ul>
		@include('includes.error_div')
		@include('includes.success_div')
		<br>
		<label><b>Order# {{\Monogram\Helper::orderNameFormatter($order)}}</b></label>
		<br>
		{!! \Monogram\Helper::getHtmlBarcode($order->short_order) !!}
		<hr style = "width: 100%; color: black; background-color:black;margin-top:  0px" size = "1" />
		{!! Form::open(['url' => url('orders/'.$order->order_id), 'method' => 'put']) !!}
		<table>
			<tr>
				<td>
					<label> Date :</label>
				</td>
				<td>
					<label style = "padding-left:54px">{{$order->order_date}}</label>
				</td>
				<td>
					<label style = "color:#ff8001;padding-left:6px">Status:</label>
				</td>
				<td>
					{!! Form::select('order_status', $statuses, \App\Status::find($order->order_status)->status_code, ['id' => 'status','style'=>'height: 16px;font-size: 10px;']) !!}
				</td>
				<td>
					<label style = "padding-left:100px">Order#</label>
				</td>
				<td>
					<label style = "padding-left:20px"> {{$order->short_order}}</label>
				</td>
				<td>
					<label style = "padding-left:50px">Customer #:</label>
					{!! Form::text('customer_id', $order->customer->id, ['id' => 'customer', 'class' => '', 'readonly' => 'readonly']) !!}

				</td>
				<td>
					<select class = "" id = "order">
						<option value = "0" selected>Reg</option>
						<option value = "1">Phone</option>
						<option value = "100">REPAIR</option>
						<option value = "101">REDO</option>
						<option value = "2">W/H</option>
						<option value = "3">D/S</option>
						<option value = "4">FB</option>
						<option value = "5">Mobile</option>
						<option value = "6">FBA</option>
					</select>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td style = "font-weight: bold;color: #686869;padding-top:15px">Ship To:</td>
				<td style = "font-weight: bold;color: #686869;padding-left:487px;padding-top:15px">Bill To:</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>Company Name</td>
				<td>{!! Form::text('ship_company_name', $order->customer->ship_company_name, ['id' => 'company_name']) !!}</td>
				<td></td>
				<!---->
				<td style = "padding-left:97px">Company Name</td>
				<td>{!! Form::text('bill_company_name', $order->customer->bill_company_name, ['id' => 'bill_company_name']) !!}</td>
				<td></td>
			</tr>
			<tr>
				<td>First/last Name</td>
				<td>{!! Form::text('ship_first_name', $order->customer->ship_first_name, ['id' => 'ship_first_name']) !!}</td>
				<td>{!! Form::text('ship_last_name', $order->customer->ship_last_name, ['id' => 'ship_last_name']) !!}</td>
				<td style = "padding-left:97px">First/last Name</td>
				<td>{!! Form::text('bill_first_name', $order->customer->bill_first_name, ['id' => 'bill_first_name', 'class' => '']) !!}</td>
				<td>{!! Form::text('bill_last_name', $order->customer->bill_last_name, ['id' => 'bill_last_name', 'class' => '']) !!}</td>
			</tr>
			<tr>
				<td>Addr1</td>
				<td>{!! Form::text('ship_address_1', $order->customer->ship_address_1, ['id' => 'ship_address_1']) !!}</td>
				<td></td>
				<td style = "padding-left:97px">Addr1</td>
				<td>{!! Form::text('bill_address_1', $order->customer->bill_address_1, ['id' => 'bill_address_1', 'class' => '']) !!}</td>
				<td></td>
			</tr>
			<tr>
				<td>Addr2</td>
				<td>{!! Form::text('ship_address_2', $order->customer->ship_address_2, ['id' => 'ship_address_2']) !!}</td>
				<td></td>
				<td style = "padding-left:97px">Addr2</td>
				<td>{!! Form::text('bill_address_2', $order->customer->bill_address_2, ['id' => 'bill_address_2']) !!}</td>
				<td></td>
			</tr>
			<tr>
				<td>City, State, Zip</td>
				<td>{!! Form::text('ship_city', $order->customer->ship_city, ['id' => 'ship_city']) !!}</td>
				<td>{!! Form::text('ship_state', $order->customer->ship_state, ['id' => 'ship_state']) !!}</td>
				<td style = "padding-left:97px">City, State, Zip</td>
				<td>{!! Form::text('bill_city', $order->customer->bill_city, ['id' => 'bill_city']) !!}</td>
				<td>{!! Form::text('bill_state', $order->customer->bill_state, ['id' => 'bill_state']) !!}</td>
			</tr>
			<tr>
				<td></td>
				<td>{!! Form::text('ship_zip', $order->customer->ship_zip, ['id' => 'ship_zip','style'=>'width: 100px']) !!}</td>
				<td></td>
				<td style = "padding-left:97px"></td>
				<td>{!! Form::text('bill_zip', $order->customer->bill_zip, ['id' => 'bill_zip','style'=>'width: 100px']) !!}</td>
				<td></td>
			</tr>
			<tr>
				<td>Country</td>
				<td>{!! Form::text('ship_country', $order->customer->ship_country, ['id' => 'ship_country']) !!}</td>
				<td></td>
				<td style = "padding-left:97px">Country</td>
				<td>{!! Form::text('bill_country', $order->customer->bill_country, ['id' => 'bill_country']) !!}</td>
				<td></td>
			</tr>
			<tr>
				<td>Phone</td>
				<td>{!! Form::text('ship_phone', $order->customer->ship_phone, ['id' => 'company_name']) !!}</td>
				<td></td>
				<td style = "padding-left:97px">Phone</td>
				<td>{!! Form::text('bill_phone', $order->customer->bill_phone, ['id' => 'bill_phone']) !!}</td>
				<td></td>
			</tr>
		</table>
		<!--{!! Form::text('ship_email', $order->customer->ship_email, ['id' => 'ship_email']) !!}-->


		<table>
			<tr>
				<td>
					<hr style = "width: 470px; color: black; background-color:black;margin-top: 10px" size = "1" />
				</td>
				<td>
					<hr style = "margin-left:58px;width: 470px; color: black; background-color:black;margin-top:  10px"
					    size = "1" />
				</td>
			</tr>
		</table>

		<table>
			<tr>
				<td>Amount</td>
				<td style = "padding-left:40px">${!!$order->total!!}</td>
				<td style = "padding-left:10px"><b>Auto tax calculation:</b></td>
				{{-- No valid data available from xml --}}
				<td>{!! Form::select('paid', ['Yes', 'No'], $order->paid, ['id' => 'paid']) !!}</td>
				<td style = "padding-left:190px">E-Mail:</td>
				<td style = "padding-left:20px">
					{!! Form::text('bill_email', $order->customer->bill_email, ['id' => 'bill_email','style'=>'width: 300px']) !!}
					@if($order->customer->bill_email)
						<button type = "button" class = "btn btn-link" data-toggle = "modal"
						        data-target = "#large-email-modal-lg">
							<i class = "fa fa-envelope-o"></i>
						</button>
					@endif
				</td>
				<!-- {!! Form::text('email', $order->customer->ship_email, ['id' => 'email', 'class' => 'form-control']) !!}
						-->
				<td></td>
			</tr>
			<tr>
				<td>Paid:</td>
				<td style = "padding-left:40px"> {!! Form::select('tax_calculation', ['Yes', 'No'], $order->paid, ['id' => 'tax_calculation']) !!}</td>
				<td><a style = "color:#ff8001" href = "#">View/Report Actual Payment</a></td>
				<td>Paid: ***</td>
				<td style = "padding-left:190px">Payment:</td>
				{{-- No value found for the following --}}
				<td style = "padding-left:20px"> {{$order->payment_method}}</td>
				<td></td>
			</tr>
		</table>

		<table>
			<tr>
				<td>Ship Via:</td>
				<td style = "padding-left:32px">
					<b>{!! Form::select('shipping', $shipping_methods, $order->customer->shipping, ['id' => 'shipping_method','style' => 'color: #FF0000;font-weight: bold; height: 16px;font-size: 10px;']) !!}</b>
					<br>
					<span>Weight *** Lbs.</span>
				</td>
				<td style = "padding-left:289px">
					<label><b>Customer comment</b></label><br>
					{!! Form::textarea('order_comments', $order->order_comments, ['id' => 'order_comments','rows' => '2', 'style'=>'width: 155px']) !!}
					<br>
					<label><b>Gift message:</b></label>
					<br>
					<textarea name = "gift_message" class = "form-control" id = "Gift message"></textarea>
					<br>
					<a style = "color:red" href = "#">View/update custom data</a>
				</td>
			</tr>
		</table>
		<hr style = "width: 100%; color: black; background-color:black;margin-top:  10px" size = "1" />
		<table id = "items-table">
			<thead>
			<tr>
				<th>Image</th>
				<th>Name</th>
				<th>Code</th>
				<th>Quantity</th>
				<th>Inv</th>
				<th>Each</th>
				<th>Options</th>
				<th>Item status</th>
				<th>B/O</th>
			</tr>
			</thead>
			<tbody>
			@setvar($ind = 0)
			@setvar($sub_total = 0)
			@foreach($order->items as $item)
				@setvar( $sub_total += ($item->item_quantity * $item->item_unit_price))
				<tr>
					{!! Form::hidden("item_id[$ind]", $item->id) !!}
					<td><img src = "{{$item->item_thumb}}" /></td>
					<td>
						<a href = "{{ url($item->product ? $item->product->product_url : "#") }}"
						   target = "_blank">{{$item->item_description}}</a>
					</td>
					<td>
						{{ $item->child_sku }} /
						<a style = 'color:red'
						   href = "{{ url(sprintf("/products?search_for=%s&search_in=product_model", $item->item_code)) }}"
						   target = "_blank">{{$item->item_code}}</a>
					</td>
					<td>{!! Form::text("previous_item_quantity[$ind]", $item->item_quantity, ['id' => 'item_quantity','style'=>'width:35px']) !!}</td>
					<td></td>
					<td>${{$item->item_unit_price}}</td>
					<td>{!! Form::textarea("item_option[$ind]", \Monogram\Helper::jsonTransformer($item->item_option), ['id' => 'item_option', 'rows' => '3','style'=>'width:150px;color:#686869;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 9px']) !!}</td>
					<td>
						{{--{!! Form::select("item_order_status[$ind]", \App\Status::where('is_deleted', 0)->lists('status_name','id'), $item->item_order_status_2, ['id' => 'order_status_id',])  !!}--}}
						@if($item->batch_number)
							<br />
							<p>
								View batch:
								<a href = "{{ url(sprintf("/batches/%d/%s", $item->batch_number, $item->station_name)) }}"
								   target = "_blank">{{ $item->batch_number }}</a>
								@if($item->station_name)
									<br />
									<span>Current station: {{ $item->station_name }}</span>
								@endif
							</p>
						@endif
					</td>
					<td></td>
					@setvar($ind++)
				</tr>
				<tr colspan = "10">
					<td>Item# {{ $item->id }}{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s", $item->id)) !!} </td>
				</tr>
				@if($item->shipInfo && $item->shipInfo->tracking_number)
					<tr>
						<td colspan = "8">Shipped on {{date("m/d/y", strtotime($item->shipInfo->postmark_date ?: "now" ))}} by {{$item->shipInfo->tracking_type}} , Trk# {{$item->shipInfo->tracking_number}}</td>
					</tr>
				@endif
			@endforeach
			</tbody>
		</table>
		<div class = "row">
			<div class = "col-md-10">
				<div class = "row">
					<div class = "col-md-12">
						<table class = "table table-bordered" id = "selected-items">
							<caption>Selected items</caption>
							<tr>
								<td>Image</td>
								<td>Id catalog</td>
								<td>Price</td>
								<td>Quantity</td>
								<td>Action</td>
							</tr>
						</table>
					</div>
				</div>
				<div class = "form-group">
					<label for = "item_sku" class = "col-md-2 control-label">SKU / Name / Id catalog</label>
					<div class = "col-md-4">
						<div class = "input-group">
							{!! Form::text('search_item_sku', null, ['id'=>'item_sku', 'class' => 'form-control', 'placeholder' => 'Search SKU / Name / Id catalog']) !!}
							<span class = "input-group-btn">
								<button class = "btn btn-info" type = "button" id = "puller">Pull</button>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class = "row" style = "margin-bottom: 30px;">
				<div class = "col-md-12">
					<a class = "btn btn-xs btn-primary pull-right" href = "#" disabled = "true"
					   id = "remove-preview">Remove preview</a>
					<table class = "table table-bordered" style = "display: none;">
						<caption id = 'search-caption'></caption>
						<thead>
						<tr>
							<th>Image</th>
							<th>Product name</th>
							<th>SKU</th>
							<th>Id catalog</th>
						</tr>
						</thead>
						<tbody id = "preview">

						</tbody>
					</table>
				</div>
			</div>
			{{-- Deleted from line: 346 --}}
			{{-- <div class = "row">
				<div class = "col-md-12">
					<table style = "margin-left:775px">
						<tr>
							<td></td>
							<td>Only insert numbers below except coupon id</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Subtotal:</td>
							<td align = "right">{!! Form::number('subtotal', 0.0, ['id' => 'subtotal', 'readonly' => 'readonly', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Coupon-discount</b>:</td>
							<td align = "right">{!! Form::text('coupon_id', null, ['placeholder' => 'Coupon id']) !!} - {!! Form::number('coupon_value', 0.0, ['id' => 'coupon_value', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Gift Wrap:</td>
							<td align = "right">{!! Form::number('gift_wrap_cost', 0.0, ['id' => 'gift_wrap_cost', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Shipping:</td>
							<td align = "right">{!! Form::number('shipping_charge', 0.0, ['id' => 'shipping_charge', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Insurance:</td>
							<td align = "right">{!! Form::number('insurance', 0.0, ['id' => 'insurance', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:45px ">Adjustments:</td>
							<td align = "right">{!! Form::number('adjustments', 0.0, ['id' => 'adjustments', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:45px ">Tax:</td>
							<td align = "right">{!! Form::number('tax_charge', 0.0, ['id' => 'tax_charge', 'step' => 'any']) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:45px ">Total:</td>
							<td align = "right">{!! Form::number('total', 0.0, ['id' => 'total', 'readonly' => 'readonly', 'step' => 'any']) !!}</td>
						</tr>
						<tr>

						</tr>
					</table>
				</div>
			</div> --}}
			<div class = "row" id = "items-holder">
			</div>
		</div>
	</div>
	<div class = "row" style = "margin-left: 150px;margin-right: 150px">
		<hr style = "width: 100%; color: black; background-color:black;margin-top:  10px" size = "1" />
		<table>
			<tr>
				<td><p style = "color:#686869;border: 1px solid;padding:4px 540px 4px 10px ">
						<b>Customer Interactions</b> <a style = 'color:#ff8001' href = "#"
						                                id = 'add-note'>(add a note/reminder)</a>
					</p>
				</td>
			</tr>
			<tr style = "display: none;">
				<td>
					{!! Form::textarea('note', null, ['id' => 'note', 'placeholder' => 'Add a note']) !!}
					<br />
					{!! Form::submit('add note', ['id' => 'instant-add-note', 'class' => 'btn btn-link', 'style' => 'display: none;']) !!}
				</td>
			</tr>
			@foreach($order->notes as $note)
				<tr>
					<td>
						({{$note->created_at}}) Note by: {{$note->user->username}}
					</td>
				</tr>
				<tr>
					<td>
						{{$note->note_text}}
					</td>
				</tr>
			@endforeach
		</table>
		<table style = "margin-left:775px">
			<tr>
				<td align = "right" style = "padding-right:40px ">Subtotal:</td>
				<td align = "right">${{sprintf("%02.2f",$sub_total)}}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:40px ">Coupon <b>({{ $order->coupon_id }})</b>:</td>
				<td align = "right">${!!sprintf("%02.2f",$order->coupon_value)!!}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:40px ">Gift Wrap:</td>
				<td align = "right">{!! Form::text('gift_wrap_cost', sprintf("%02.2f",$order->gift_wrap_cost), ['id' => 'gift_wrap_cost','style'=>'width:60px']) !!}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:40px ">Shipping:</td>
				<td align = "right">${{sprintf("%02.2f",$order->shipping_charge)}}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:40px ">Insurance:</td>
				<td align = "right">$ {!! Form::text('insurance', sprintf("%0.2f", $order->insurance), ['id' => 'insurance','style'=>'width:60px']) !!}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:45px ">Adjustments:</td>
				<td align = "right">$ {!! Form::text('adjustments', sprintf("%02.2f",$order->adjustments), ['id' => 'adjustments','style'=>'width:60px']) !!}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:45px ">Tax:</td>
				<td align = "right">${{sprintf("%02.2f",$order->tax_charge)}}</td>
			</tr>
			<tr>
				<td align = "right" style = "padding-right:45px ">Total:</td>
				<td align = "right">${{sprintf("%02.2f",$order->total)}}</td>
			</tr>
			<tr>

			</tr>
		</table>
		<div align = "right">
			<button type = "submit" class = "btn btn-primary">Update Order</button>
		</div>
		<table style = "margin-bottom: 30px;">
			<tr>
				<td>
					<a href = "{{ url(sprintf('prints/packing/%s', $order->order_id)) }}">Print Packing slip</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href = "{{ url(sprintf('prints/invoice/%s', $order->order_id)) }}">Print Invoice</a>
				</td>
			</tr>
		</table>
	</div>
	</div>
	{!! Form::close() !!}
	<div class = "col-md-12">

	</div>
	<div class = "row">
		<div class = "col-md-12">
			<div class = "modal fade bs-example-modal-lg" id = "large-email-modal-lg" tabindex = "-1" role = "dialog"
			     aria-labelledby = "large-modal">
				<div class = "modal-dialog modal-lg">
					<div class = "modal-content">
						<div class = "modal-header">
							<button type = "button" class = "close" data-dismiss = "modal" aria-label = "Close">
								<span aria-hidden = "true">×</span>
							</button>
							<h4 class = "modal-title">Send email to customer</h4>
						</div>
						<div class = "modal-body">
							{!! Form::open(['url' => '/orders/send_mail', 'id' => 'email-sender-form']) !!}
							{!! Form::hidden('order_id', $order->order_id) !!}
							<table class = "table table-bordered">
								<tr>
									<td>Email</td>
									<td>{!! Form::text('recipient', $order->customer->bill_email, ['id' => 'email-recipient', 'class' => 'form-control']) !!}</td>
								</tr>
								<tr>
									<td>Message type</td>
									<td>
										{!! Form::select('message_types', $message_types, null, ['id' => 'message-types', 'class' => 'form-control']) !!}
									</td>
								</tr>
								<tr>
									<td>Subject</td>
									<td> {!! Form::text('subject', sprintf("Order %s", $order->short_order), ['id' => 'email-subject', 'class' => 'form-control', 'data-default-subject' =>  sprintf("Order %s", $order->short_order)]) !!} </td>
								</tr>
								<tr>
									<td></td>
									<td>
										{!! Form::textarea('message', null, ['id' => 'email-message', 'class' => 'form-control' ]) !!}
									</td>
								</tr>
							</table>
							{!! Form::close() !!}
						</div>
						<div class = "modal-footer">
							<button type = "button" class = "btn btn-default" data-dismiss = "modal"
							        id = "dismiss-email">Close
							</button>
							<button type = "button" class = "btn btn-primary" id = "send-email">Send</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src = "//cdn.ckeditor.com/4.5.9/standard/ckeditor.js"></script>
	<script type = "text/javascript" src = "/assets/js/nprogress.js"></script>
	<script type = "text/javascript">
		var editor = null;
		function initializeCkeditor ()
		{
			editor = CKEDITOR.replace('email-message');
		}

		initializeCkeditor();

		function reInitializeCkeditor ()
		{
			CKEDITOR.remove(CKEDITOR.instances['email-message']);
			$("#cke_email-message").remove();
			setTimeout(initializeCkeditor, 100);
		}

		function setEmailMessageSubject (subject)
		{
			$("#email-subject").val(subject);
		}

		function setEmailMessageToEditor (message)
		{
			CKEDITOR.instances['email-message'].setData(message);
		}

		function setEmailMessageToTextArea (message)
		{
			$("#email-message").val(message);
		}

		// on message type select change
		$("#message-types").on('change', function (event)
		{
			var message_type = $(this).val();
			if ( message_type == 0 || message_type == undefined ) {
				// no action is selected.
				reInitializeCkeditor();
				setEmailMessageSubject($("#email-subject").attr('data-default-subject'));
				return;
			}
			var url = "/orders/mailer";
			var order_id = "{{ $order->order_id }}";
			var data = {
				order: order_id, message_type: message_type, _token: "{{ csrf_token() }}"
			};
			var method = "POST";
			ajax(url, method, data, emailMessageTypeSelectionSuccessHandler, emailMessageTypeSelectionErrorHandler);
		});

		function emailMessageTypeSelectionSuccessHandler (data)
		{
			setEmailMessageSubject(data.subject);
			setEmailMessageToEditor(data.message);
		}

		function emailMessageTypeSelectionErrorHandler (data)
		{
			console.log(data);
			alert("Something went wrong!");
		}

		editor.on('change', function (event)
		{
			setEmailMessageToTextArea(event.editor.getData());
		});

		$("#send-email").on('click', function (event)
		{
			ajax("/orders/send_mail", "POST", $("#email-sender-form").serialize(), emailMessageSuccessHandler, emailMessageErrorSendHandler);
		});

		function emailMessageErrorSendHandler (data)
		{
			$("#large-email-modal-lg").modal('toggle');
			alert("Something went wrong!");
		}

		function emailMessageSuccessHandler (data)
		{
			$("#large-email-modal-lg").modal('toggle');
			alert("Mail was sent to the customer.");
		}
	</script>
	<script type = "text/javascript">
		$("a#add-note").on('click', function (event)
		{
			event.preventDefault();
			$("textarea#note").closest('tr').show();
			$(this).hide();
			$("#instant-add-note").show();
		});
	</script>
	<script type = "text/javascript">
		var searched = '';
		var timeout = null;
		$("#puller").on('click', function ()
		{
			requestSearchOnServer($("#item_sku"));
		});

		function requestSearchOnServer (node)
		{
			// trim the input field value
			var sku = $(node).val().trim();
			// if the sku searching for is empty
			// don't proceed
			if ( sku == "" ) {
				return;
			}
			// if the previous query and the current is same,
			// like, control + a is pressed
			// don't proceed
			/*if ( searched == sku ) {
			 return;
			 }*/
			// else set the value to global searched variable
			// and proceed
			searched = sku;
			if ( timeout ) {
				clearTimeout(timeout);
			}
			timeout = setTimeout(function ()
			{
				removePreview();
				var url = "/orders/ajax";
				var data = {
					"sku": sku
				};
				var method = "GET";
				ajax(url, method, data, setAjaxResult, searchProductError);
			}, 200);
		}

		$("#item_sku").on('keydown keyup', function (event)
		{
			if ( event.keyCode == 13 ) {
				event.preventDefault();
				$("#puller").click();
			}
		});

		$("#item_sku").on('paste keyup', function (event)
		{
			//requestSearchOnServer($(this));
		});

		function ajax (url, method, data, successHandler, errorHandler)
		{
			NProgress.start();
			$.ajax({
				url: url, method: method, data: data, success: function (data, status)
				{
					NProgress.done();
					successHandler(data);
				}, error: function (xhr, status, error)
				{
					NProgress.done();
					errorHandler(xhr);
				}
			})
		}

		function searchProductError (xhr)
		{
			hideTable();
			alert('Nothing found.');
		}

		function hideTable ()
		{
			$("#remove-preview").attr('disabled', true);
			getPreviewAbleNode().closest('table').hide();
		}

		function showTable ()
		{
			$("#remove-preview").removeAttr("disabled");
			getPreviewAbleNode().closest('table').show()
		}

		function getPreviewAbleNode ()
		{
			return $("#preview");
		}

		function removePreview ()
		{
			getPreviewAbleNode().empty();
		}

		function setAjaxResult (data)
		{
			if ( data.search != searched ) {
				return;
			}
			removePreview();
			showTable();
			showSearchCaption(data);
			$.each(data.products, function (key, value)
			{
				var node = "<tr data-store-name='" + (value.store && value.store.store_name.toLowerCase()) + "' data-id-catalog='" + value.id_catalog + "' data-sku = '" + value.product_model + "'>" + "<td><img width='50' height='50' src='" + value.product_thumb + "'</td>" + "<td>" + value.product_name + "</td>" + "<td>" + value.product_model + "</td>" + "<td>" + value.id_catalog + "</td>" + "</tr>";
				getPreviewAbleNode().append(node);
			});
		}

		function getSearchCaption ()
		{
			return $("#search-caption");
		}

		function emptySearchCaption ()
		{
			getSearchCaption().empty();
		}

		function setSearchCaption (message)
		{
			emptySearchCaption();
			getSearchCaption().text(message);
		}

		function showSearchCaption (data)
		{
			var count = data.products.length;
			count = count || 0;
			var message = "Searched: \"" + data.search + "\" - " + count + " results found.";
			setSearchCaption(message);
		}

		$(document).on('click', "#preview tr", function ()
		{
			var answer = askPermission();
			// didn't want to add this product
			// abort
			if ( !answer ) {
				return;
			}
			var id_catalog = $(this).attr('data-id-catalog');
			var store_name = $(this).attr('data-store-name');
			var sku = $(this).attr('data-sku');
			var url = "/orders/product_info";
			var data = {
				"id_catalog": id_catalog, "sku": sku, "store_name": store_name
			};
			var method = "GET";

			ajax(url, method, data, fetchProductInformationOnSelect, showProductInformationFetchFailed);

		});

		function askPermission (message)
		{
			message = message || "Add this product to list?";
			var answer = confirm(message);
			return answer;
		}

		$(document).on('mouseenter', '#preview tr', function (event)
		{
			$(this).css('cursor', 'pointer');
		}).on('mouseleave', '#preview tr', function (event)
		{
			$(this).css('cursor', 'auto');
		});

		function showProductInformationFetchFailed (xhr)
		{
			alert("Product not found or Something went wrong!");
		}

		function fetchProductInformationOnSelect (data)
		{
			var result = data.result;
			if ( result == false ) {
				alert('Something went wrong!');
			} else {
				$("#items-holder").append(result);
				var unique_modal_class = data.unique_modal_class;
				$("." + unique_modal_class).modal({
					backdrop: 'static', keyboard: false, show: true
				});
			}
		}

		$(document).on('click', '.cancel', function ()
		{
			removeModalBody($(this));
		});

		function removeModalBody (node)
		{
			$(node).closest('div.modal-content').find('div.modal-body').remove();
		}

		$("#remove-preview").on('click', function (event)
		{
			event.preventDefault();
			hideTable();
		});

		function getSelectedItemsTableNode ()
		{
			return $("#selected-items");
		}

		$(document).on('click', 'button.add-item', function ()
		{
			var body = $(this).closest('div.modal-content').find('div.modal-body');
			var quantity = parseInt(body.find('input[type=number]').val());
			if ( quantity == 0 ) {
				alert('Quantity cannot be zero');
			} else {
				// get the subtotal price from the input text
				var sub_total_price = getSubtotalNodeValue();
				var modal_class = body.find('.hidden_unique_modal_class').val();
				var item_image = body.find(".item_image").val();
				var item_id_catalog = body.find(".item_id_catalog").val();
				var item_price = parseFloat(body.find(".item_price").val());
				sub_total_price += quantity * item_price;
				setSubtotalValue(sub_total_price);
				var tr = "<tr data-modal-class='" + modal_class + "'>" + "<td> <img src='" + item_image + "' width='50' height='50' /> </td>" + "<td>" + item_id_catalog + "</td>" + "<td class='price_on_table'>" + item_price + "</td>" + "<td class='quantity_on_table'>" + quantity + "</td>" + "<td>" + "<a href='#' class='delete-row'>Delete</a>" + "</td>";
				getSelectedItemsTableNode().append(tr);
				body.closest('.modal').modal('hide');
				hideTable();
			}
		});

		$(document).on('click', '.delete-row', function (event)
		{
			event.preventDefault();
			var tr = $(this).closest('tr');
			var modal_class = tr.attr('data-modal-class');
			var answer = askPermission("Are you sure want to delete?");
			if ( answer ) {
				var price = parseFloat(tr.find(".price_on_table").text());
				var quantity = parseInt(tr.find(".quantity_on_table").text());
				var sub_total = getSubtotalNodeValue();
				sub_total -= (price * quantity);
				setSubtotalValue(sub_total);
				$("." + modal_class).find('.modal-body').remove();
				tr.remove();
			}
		});

		getSubtotalNode().on('change keyup', function ()
		{
			calculateTotal();
		});
		getCouponValueNode().on('change keyup', function ()
		{
			calculateTotal();
		});
		getGiftWrapCostNode().on('change keyup', function ()
		{
			calculateTotal();
		});
		getShippingChargeNode().on('change keyup', function ()
		{
			calculateTotal();
		});
		getInsuranceNode().on('change keyup', function ()
		{
			calculateTotal();
		});
		getAdjustmentNode().on('change keyup', function ()
		{
			calculateTotal();
		});
		getTaxChargeNode().on('change keyup', function ()
		{
			calculateTotal();
		});


		function setSubtotalValue (value)
		{
			getSubtotalNode().val(value.toFixed(2)).change();
		}
		function getSubtotalNode ()
		{
			return $("#subtotal");
		}
		function getSubtotalNodeValue ()
		{
			return parseFloat(getSubtotalNode().val());
		}

		function getCouponValueNode ()
		{
			return $("#coupon_value");
		}
		function getCouponValueNodeValue ()
		{
			return parseFloat(getCouponValueNode().val());
		}

		function getGiftWrapCostNode ()
		{
			return $("#gift_wrap_cost");
		}
		function getGiftWrapCostNodeValue ()
		{
			return parseFloat(getGiftWrapCostNode().val());
		}

		function getShippingChargeNode ()
		{
			return $("#shipping_charge");
		}
		function getShippingChargeNodeValue ()
		{
			return parseFloat(getShippingChargeNode().val());
		}

		function getInsuranceNode ()
		{
			return $("#insurance");
		}
		function getInsuranceNodeValue ()
		{
			return parseFloat(getInsuranceNode().val());
		}

		function getAdjustmentNode ()
		{
			return $("#adjustments");
		}
		function getAdjustmentNodeValue ()
		{
			return parseFloat(getAdjustmentNode().val());
		}

		function getTaxChargeNode ()
		{
			return $("#tax_charge");
		}
		function getTaxChargeNodeValue ()
		{
			return parseFloat(getTaxChargeNode().val());
		}

		function getTotalValueNode ()
		{
			return $("#total");
		}
		function setTotalValue (val)
		{
			getTotalValueNode().val(val.toFixed(2));
		}

		function calculateTotal ()
		{
			var total = getSubtotalNodeValue() - getCouponValueNodeValue() + getGiftWrapCostNodeValue() + getShippingChargeNodeValue() + getInsuranceNodeValue() + getAdjustmentNodeValue() + getTaxChargeNodeValue();
			setTotalValue(total);

		}
	</script>
</body>
</html>