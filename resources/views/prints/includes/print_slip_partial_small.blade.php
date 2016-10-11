<div class = "current-batch">
	<table cellpadding = "0" cellspacing = "0" width = "100%" border = "0">
		<tr valign = "top">
			<td style = "width:100%;">
				<table cellpadding = "0" cellspacing = "0" width = "100%" border = "0">
					<tr valign = "top">
						<td style = " width:15%; height:1px;"></td>
						<td style = " width:32%; height:1px;"></td>
						<td style = " width:10%; height:1px;"></td>
						<td style = " width:40%; height:1px;"></td>
					</tr>
					<tr valign = "top">
						<td colspan = "3">
							@if($order->store_id == "yhst-132060549835833")
								ShopOnlineDeals.com
							@elseif($order->store_id == "yhst-128796189915726")
								MonogramOnline.com
							@endif
							<br />
								575 Underhill Blvd <br>
								Suite 216<br> Syosset, NY 11791
							<br />
							@if($order->store_id == "yhst-132060549835833")
								cs@ShopOnlineDeals.com <br>
								www.ShopOnlineDeals.com
							@elseif($order->store_id == "yhst-128796189915726")
								cs@MonogramOnline.com <br>
								www.MonogramOnline.com
							@endif
						</td>
						<td colspan = "1" align = "right" style = "padding-top:10px;">
							<img src = "{{url(sprintf('/assets/images/%s.jpg', $order->store_id))}}"
							     border = "0" style="height: 70%; width: 85%;">
						</td>
					</tr>
					<tr valign = "top">
						<td colspan = "4" align = "center"><strong>
								Packing Slip for order#
								{{$order->short_order}} </strong>
							<hr size = "1">
						</td>
					</tr>
					<tr valign = "top">
						<td><strong>Order Date:</strong></td>
						<td>{{date("m/d/y", strtotime($order->order_date) )}}</td>
						<td><strong>Order #</strong></td>
						<td>
							<table width = "100%" cellpadding = "0" cellspacing = "0" border = "0">
								<tr valign = "top">
									<td align = "left"><strong>{{$order->short_order}}</strong></td>
									<td align = "right">
										Shipping# {{\Monogram\Helper::orderNameFormatter($order)."-0"}}
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr valign = "top">
						<td><strong>Ship to:</strong></td>
						<td>
							@if($order->customer->ship_company_name)
								{{$order->customer->ship_company_name}}<br>
							@endif
							{{$order->customer->ship_full_name}}<br>
							{{$order->customer->ship_address_1}}<br>
							@if($order->customer->ship_address_2)
								{{$order->customer->ship_address_2}}<br>
							@endif
							{{$order->customer->ship_city}} {{$order->customer->ship_state}}  {{$order->customer->ship_zip}}
							<br>
							{{$order->customer->ship_country}}<br>
							{{$order->customer->ship_phone}}
						</td>
						<td><strong>Bill To:</strong></td>
						<td>
							@if($order->customer->bill_company_name)
								{{$order->customer->bill_company_name}}<br>
							@endif
							{{$order->customer->bill_full_name}}<br>
							{{$order->customer->bill_address_1}}<br>
							@if($order->customer->bill_address_2)
							{{$order->customer->bill_address_2}}<br>
							@endif
							{{$order->customer->bill_city}} {{$order->customer->bill_state}}  {{$order->customer->bill_zip}}
							<br>
							{{$order->customer->bill_country}}<br>
							{{$order->customer->bill_phone}}
						</td>
					</tr>
					<tr valign = "top">
						<td><strong>Ship Via:</strong></td>
						<td colspan = "3">{{$order->customer->shipping}}</td>
					</tr>


					<tr valign = "top">
						<td colspan = "5">
							<table width = "100%" cellpadding = "2" cellspacing = "0" style="border-style: dotted;">
								<tr valign = "top" style="border-style: dotted;">
									<td align = "left"><strong>Name</strong></td>
									<td align = "left"><strong>Code</strong></td>
									<td align = "right"><strong>Qty</strong></td>
									<td align = "left"><strong>Options</strong></td>
								</tr>
								@foreach($order->items as $item)
									<tr valign = "top">
										<td align = "left" >
											{{$item->item_description}}
											@if($item->shipInfo)
												<br />
												Shipped on {{substr($item->shipInfo->transaction_datetime, 0, 10)}} by
												{{$item->shipInfo->mail_class}}
												<br />
												Trk# <a href = "#">{{$item->shipInfo->tracking_number}}</a>
											@endif
										</td>
										<td align = "left" >{{$item->item_code}}</td>
										<td align = "right" style = "font-size:18px;">
											<strong>{{$item->item_quantity}}</strong>
										</td>
										<td align = "left" >
											{!! \Monogram\Helper::jsonTransformer($item->item_option, "<br/>") !!}
										</td>
									</tr>

								@endforeach
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<script>
		window.print();
	</script>
</div>