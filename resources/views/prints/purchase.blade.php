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
							Invoice Slip for purchase#
							{{$purchase->id}} </strong>
						<hr size = "1">
					</td>
				</tr>
				<tr valign = "top">
					<td><strong>Date:</strong></td>
					<td>{{date("m/d/y", strtotime("now"))}}</td>
					<td></td>
					<td><strong>Purchase#</strong></td>
					<td>
						<table width = "100%" cellpadding = "0" cellspacing = "0" border = "0">
							<tr valign = "top">
								<td align = "left"><strong>{{$purchase->id}}</strong></td>
								<td align = "right">
									{{--<img src = "{{\Monogram\Helper::getImageBarcodeSource($order->short_order)}}">--}}
									{!! \Monogram\Helper::getHtmlBarcode($purchase->id, 2) !!}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr valign = "top">
					<td><strong>Vendor details:</strong></td>
					<td>
						{{$purchase->vendor_details->vendor_name}}<br>
						{{$purchase->vendor_details->email}}<br>
						{{$purchase->vendor_details->zip_code}} {{$purchase->vendor_details->state}}
						<br>
						{{$purchase->vendor_details->country}}
						<br>
						{{$purchase->vendor_details->phone_number}}
					</td>
					<td></td>
					<td><strong>LC:</strong> </td>
					<td>{{$purchase->lc_number}}</td>
				</tr>
				<tr valign = "top">
					<td></td>
					<td></td>
					<td></td>
					<td><strong>Insurance:</strong> </td>
					<td>{{$purchase->insurance_number}}</td>
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
								<td align = "right"><strong>Unit price</strong></td>
								<td align = "right"><strong>Sub total</strong></td>
								{{--<td align = "left"><strong>Options</strong></td>--}}
								{{--<td align = "left"><strong>B/O</strong></td>--}}
							</tr>
							<tr height = "10" valign = "top">
								<td colspan = "9">
									<hr size = "1">
								</td>
							</tr>
							@foreach($purchase->products as $row)
								@setvar($product = $row->product_details)
								<tr valign = "top">
									<td>
										<img src = "{{url("/assets/images/box.jpg")}}" border = "0">
									</td>
									<td>
										<img height = "80" width = "80"
										     src = "{{$product->product_thumb}}"
										     border = "0" />
									</td>
									<td align = "left">
										{{$product->product_name}}
										{{--@if($item->shipInfo)
											<br/>
											Shipped on {{substr($item->shipInfo->transaction_datetime, 0, 10)}} by
											{{$item->shipInfo->mail_class}}
											<br/>
											Trk# <a href="#">{{$item->shipInfo->tracking_number}}</a>
										@endif--}}
									</td>
									<td align = "left">{{$product->product_model}}</td>
									<td align = "right" style = "font-size:18px;">
										<strong>{{sprintf("%d", $row->quantity)}}</strong>
									</td>
									<td align = "right" style = "font-size:18px;">
										<strong>{{sprintf("%.2f", $row->price)}}</strong>
									</td>
									<td align = "right" style = "font-size:18px;">
										<strong>{{sprintf("%.2f", $row->sub_total)}}</strong>
									</td>
									{{--<td align = "left"></td>
									<td align = "left"></td>--}}
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