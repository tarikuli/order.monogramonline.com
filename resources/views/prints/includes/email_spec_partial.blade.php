<div class = "current-batch">
	<table cellpadding = "0" cellspacing = "0" width = "810" border = "0">
		<tr valign = "top">
			<td style = "width:800px;">
				<table cellpadding = "0" cellspacing = "0" width = "100%" border = "0">
					<tr valign = "top">
						<td style = " width:15%; height:1px;"></td>
						<td style = " width:30%; height:1px;"></td>
						<td style = " width:5%; height:1px;"></td>
						<td style = " width:7%; height:1px;"></td>
						<td style = " width:40%; height:1px;"></td>
					</tr>
					<tr valign = "top"></tr>
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
						<td colspan = "2" align = "right" style = "padding-top:10px;">
							<img src = "{{url(sprintf('/assets/images/%s.jpg', $order->store_id))}}"
							     border = "0">
						</td>
					</tr>
					<tr valign = "top">
						<td colspan = "5" align = "center"><strong>
								Shipping Confirmation for order#
								{{$order->short_order}} </strong>
							<hr size = "1">
						</td>
					</tr>
					<tr valign = "top">
						<td><strong>Order Date:</strong></td>
						<td>{{date("m/d/y", strtotime($order->order_date) )}}</td>
						<td>

						</td>
						<td><strong>Order #</strong></td>
						<td>
							<table width = "100%" cellpadding = "0" cellspacing = "0" border = "0">
								<tr valign = "top">
									<td align = "left"><strong>{{$order->short_order}}</strong></td>
									<td align = "right">
										{!! \Monogram\Helper::getHtmlBarcode(\Monogram\Helper::orderNameFormatter($order)."-0") !!}
										<br>Shipping# {{\Monogram\Helper::orderNameFormatter($order)."-0"}}
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr valign = "top">
						<td><strong>Shipping Address:</strong></td>
						<td>
							{{$order->customer->ship_full_name}}<br>
							{{$order->customer->ship_address_1}}<br>
							{{$order->customer->ship_city}} {{$order->customer->ship_state}}  {{$order->customer->ship_zip}}
							<br>
							{{$order->customer->ship_country}}<br>
							{{$order->customer->ship_phone}}
						</td>
						<td></td>
						<td><strong>Billing Address:</strong></td>
						<td>
							{{$order->customer->bill_full_name}}<br>
							{{$order->customer->bill_address_1}}<br>
							{{$order->customer->bill_city}} {{$order->customer->bill_state}}  {{$order->customer->bill_zip}}
							<br>
							{{$order->customer->bill_country}}<br>
							{{$order->customer->bill_phone}}
						</td>
					</tr>
					<tr valign = "top">
						<td><strong>Ship Via:</strong></td>
						<td>{{$order->customer->shipping}}</td>
						<td></td>
						<td><strong>Comments:</strong></td>
						<td>{{$order->order_comments}}</td>
					</tr>


					<tr valign = "top">
						<td colspan = "5">
							<table width = "100%" cellpadding = "2" cellspacing = "0" border = "0">
								<tr height = "10" valign = "top">
									<td colspan = "9">
										<img src = "{{url('/assets/images/spacer.gif')}}"
										     width = "50" height = "20" border = "0">
									</td>
								</tr>
								<tr valign = "top">
									<td></td>
									<td></td>
									<td align = "center"><strong>Name</strong></td>
									<td align = "center"><strong>Code</strong></td>
									<td align = "center"><strong>Item Price</strong></td>
									<td align = "center"><strong>Qty</strong></td>
									<td align = "center"><strong>Total</strong></td>
								</tr>
								<tr height = "10" valign = "top">
									<td colspan = "9">
										<hr size = "1">
									</td>
								</tr>
								@foreach($order->items as $item)
									<tr valign = "top">
										<td>
											<img src = "{{url("/assets/images/box.jpg")}}" border = "0">
										</td>
										<td>
											<img height = "80" width = "80"
											     src = "{{$item->item_thumb}}"
											     border = "0" />
										</td>
										<td align = "left">
											{{$item->item_description}}
											{!! \Monogram\Helper::jsonTransformer($item->item_option, "<br/>") !!}
											@if($item->shipInfo)
												<br />
												Shipped on {{substr($item->shipInfo->transaction_datetime, 0, 10)}} by
												{{$item->shipInfo->mail_class}}
												<br />
												Trk# <a href = "#">{{$item->shipInfo->tracking_number}}</a>
											@endif
										</td>
										{{-- SKU --}}
										<td align = "left">{{$item->item_code}}</td>
										{{-- item unit price --}}
										<td align = "right">{{$item->item_unit_price}}</td>
										{{-- QTY --}}
										<td align = "right" style = "font-size:18px;">
											<strong>{{$item->item_quantity}}</strong>
										</td>
										{{-- Total --}}
										<td align = "right">{{ (($item->item_quantity)  * ($item->item_unit_price) )}}</td>
									</tr>

									<tr>
										<td colspan = "7" align = "left" valign = "top">
											 Item# {{ $item->id}}

											@if($item->tracking_number)
												<a href = "{{ url(sprintf("http://webtrack.dhlglobalmail.com/?trackingnumber=%s", $item->tracking_number)) }}" target = "_blank"> {{ $item->tracking_number }} </a>
											@endif
										</td>
									</tr>

								@endforeach

								<tr>
									<td colspan = "7" align = "left" valign = "top">
										<table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tbody>
											<tr>
												<td width="90%"> <p><strong>Subtotal:</strong>&nbsp;</p></td>
												<td nowrap="nowrap"> <p>{{$order->sub_total}} </p></td>
											</tr>
											<tr>
												<td width="90%"><p><strong>Coupon:</strong>{{$order->coupon_id}}</p></td>
												<td nowrap="nowrap"><p>{{$order->coupon_value}} </p></td>
											</tr>
											<tr>
												<td><p><strong>Tax:</strong>&nbsp;</p></td>
												<td nowrap="nowrap"><p>{{$order->tax_charge}} </p></td>
											</tr>
											<tr>
												<td><p><strong>Shipping Cost:</strong>&nbsp;</p></td>
												<td nowrap="nowrap"><p>{{$order->shipping_charge}}</p></td>
											</tr>
											<tr>
												<td><p><strong>Total:</strong>&nbsp;</p></td>
												<td nowrap="nowrap"><p>{{$order->total}}</p></td>
											</tr>

										</tbody>
										</table>
									</td>
								</tr>

								<tr valign = "top">
									<td colspan = "7">
										<hr size = "1">
									</td>
								</tr>


								<tr valign = "top">
									<td align = "center" colspan = "7">
										<table width = "100%" cellpadding = "5" cellspacing = "5" border = "1">
											<tr valign = "top">
												<td align = "center"><p style = "text-align: center;">
														<strong>IMPORTANT PLEASE NOTE:&nbsp; </strong></p>
													<p style = "text-align: center;">
														Each item is shipped in a separate envelope.&nbsp;</p>
													<p style = "text-align: center;">
														If you have placed an order of more than one item(s) in the SAME ORDER,&nbsp;</p>
													<p style = "text-align: center;">
														You will receive each item in a separate package.&nbsp;</p>
													<p style = "text-align: center;">
														We thank you for your business and we hope you enjoy your new MonogramOnline.com product. &nbsp;</p>
													<p style = "text-align: center;">
														If for any unlikely&nbsp;reason you are not satisfied with your order please contact us through our website and we will make all efforts to make sure that you are satisfied with your purchase at MonogramOnline.com.</p>
												</td>
											</tr>
										</table>
									</td>
								</tr>
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