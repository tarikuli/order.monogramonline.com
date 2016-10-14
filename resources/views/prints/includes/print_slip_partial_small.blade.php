<div class = "current-batch" style="width: 100mm; height: 150">
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
						<td>
							{{date("m/d/y", strtotime($order->order_date) )}}
							<br>Shipping# {{\Monogram\Helper::orderNameFormatter($order)."-0"}} 
						</td>
						<td></td>
						<td>
							<table width = "100%" cellpadding = "0" cellspacing = "0" border = "0">
								<tr valign = "top">
									<td align = "right">
										{!! \Monogram\Helper::getHtmlBarcode(\Monogram\Helper::orderNameFormatter($order)."-0") !!}
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
							<table width = "100%" cellpadding = "0" cellspacing = "1"  >
								<tr valign = "top" style="border-style: dotted;">
									<td align = "justify"><strong>Discription</strong></td>
									<td align = "right"><strong>QTY</strong></td>
								</tr>
								@foreach($order->items as $item)
									<tr valign = "top" style="outline: thin dotted" >
										<td align = "left" style="max-width: 102mm;" >
											@if($item->batch_number)
												Barch# {{ $item->batch_number }} <br>
											@endif
											SKU# {{$item->item_code}}<br>
											Name: {{$item->item_description}}<br>
											{!! \Monogram\Helper::jsonTransformer($item->item_option, "<br/>") !!}
										</td>
										<td align = "right" style = "font-size:18px; max-width: 3mm;">
											<strong>{{$item->item_quantity}}</strong><br/>
											<img height = "8" width = "8"
											     src = "{{$item->item_thumb}}"
											     border = "0" />
											
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