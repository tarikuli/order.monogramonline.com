<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<meta http-equiv = "Content-Type" content = "text/html; charset=utf-8" />
<style>

	body {
		margin: 10px;
		font-family: "Times New Roman", Times, serif; /*Verdana, Arial, Helvetica, sans-serif; */
		font-size: 14px;
	}

	td {
		font-size: 14px;
	}
</style>
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
					<td colspan = "3"> MonogramOnline.com <br>
					                   575 Underhill Blvd <br>
					                   Suite 216<br> Syosset, NY 11791
						<br />
					                   cs@MonogramOnline.com <br>
					                   www.MonogramOnline.com
					</td>
					<td colspan = "2" align = "right" style = "padding-top:10px;">
						<img src = "{{url('/assets/images/yhst-128796189915726.jpg')}}"
						     border = "0">
					</td>
				</tr>
				<tr valign = "top">
					<td colspan = "5" align = "center"><strong>
							Packing Slip for order#
							{{$order->short_order}} </strong>
						<hr size = "1">
					</td>
				</tr>
				<tr valign = "top">
					<td><strong>Date:</strong></td>
					<td>{{date("m/d/y", strtotime("now"))}}</td>
					<td></td>
					<td><strong>Order #</strong></td>
					<td>
						<table width = "100%" cellpadding = "0" cellspacing = "0" border = "0">
							<tr valign = "top">
								<td align = "left"><strong>{{$order->short_order}}</strong></td>
								<td align = "right">
									{{--<img src = "{{\Monogram\Helper::getImageBarcodeSource($order->short_order)}}">--}}
									{!! \Monogram\Helper::getHtmlBarcode($order->short_order) !!}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr valign = "top">
					<td><strong>Ship to:</strong></td>
					<td>
						{{$order->customer->ship_full_name}}<br>
						{{$order->customer->ship_address_1}}<br>
						{{$order->customer->ship_city}} {{$order->customer->ship_state}}  {{$order->customer->ship_zip}}
						<br>
						{{$order->customer->ship_country}}<br>
						{{$order->customer->ship_phone}}
					</td>
					<td></td>
					<td><strong>Bill To:</strong></td>
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
								<td align = "left"><strong>Name</strong></td>
								<td align = "left"><strong>Code</strong></td>
								<td align = "right"><strong>Qty</strong></td>
								<td align = "right"></td>
								<td align = "left"><strong>Options</strong></td>
								<td align = "left"><strong>B/O</strong></td>
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
										@if($item->shipInfo)
											<br/>
											Shipped on {{substr($item->shipInfo->transaction_datetime, 0, 10)}} by
											{{$item->shipInfo->mail_class}}
											<br/>
											Trk# <a href="#">{{$item->shipInfo->tracking_number}}</a>
										@endif
									</td>
									<td align = "left">{{$item->item_code}}</td>
									<td align = "right" style = "font-size:18px;">
										<strong>{{$item->item_quantity}}</strong>
									</td>
									<td align = "right"></td>
									<td align = "left">
										{!! \Monogram\Helper::jsonTransformer($item->item_option, "<br/>") !!}
									</td>
									<td align = "left"></td>
								</tr>
							@endforeach
							<tr valign = "top">
								<td colspan = "9">
									<hr size = "1">
								</td>
							</tr>


							<tr valign = "top">
								<td align = "center" colspan = "9">
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
	//window.print();
</script>
</html>