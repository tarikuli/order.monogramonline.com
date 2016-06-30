<div class = "current-batch">
	<!-- table width = "100%" cellpadding = "2" cellspacing = "2" border = "0"-->
	<table style = "width:210mm;" cellpadding = "2" cellspacing = "2" border = "0">
		<tr valign = "top">
			<td colspan = "10">
				<table width = "100%" cellpadding = "2" cellspacing = "2" border = "0">
					<tr valign = "top">
						<td align = "left" width = "150 ">Batch #</td>
						<td><a href = "{{url("/batch_details/$batch_number")}}">{{$batch_number}}</a></td>
						<td rowspan = "3"
						    align = "right">{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s", $batch_number)) !!}</td>
					</tr>
					<tr>
						<td align = "left">Batch creation date</td>
						<td>{{substr($item->batch_creation_date, 0, 10)}}</td>
					</tr>
					<tr>
						<td align = "left">Status</td>
						<td>{{ $batch_status }} {{--ucfirst($items->first()->item_order_status)--}} {{--;&nbsp;&nbsp;Last updated by: Rosemarie@monogramonline.com--}}</td>
						<td></td>
					</tr>
					<tr>
						<td align = "left">Route</td>
						<td colspan = "2">{{$route['batch_code']}} / {{$route['batch_route_name']}} => {!! $stations !!}</td>
					</tr>
					<tr valign = "middle">
						<td align = "left">Current station</td>
						<td colspan = "2"
						    align = "left"> {{$current_station_name}};&nbsp;&nbsp;Current station since: {{\Monogram\Helper::getStationLog($batch_number, $current_station_name)}}</td>
					</tr>
					<tr>
						<td align = "left">Next station</td>
						<td>{{$next_station_name}}</td>
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table style = "width:210mm;" cellpadding = "2" cellspacing = "2" border = "0">
					<tr valign = "top">
						<th align = "center" style = "width:12mm;">Order</th>
						<th align = "center" style = "width:17mm;">Date</th>
						<th align = "center" style = "width:5mm;">Qty</th>
						<th align = "center" style = "width:18mm;">SKU</th>
						<th align = "center" style = "width:50mm;">Item name</th>
						<th align = "center" style = "width:100mm;">Options</th>
						<th align = "center" style = "width:8mm;">Shi -pped?</th>
					</tr>
					@setvar($count = 0)
					@foreach($item->groupedItems as $row)
						@if(!$station_name || $row->station_name == $station_name)

						@if($row->shipInfo)
						@if(!$row->shipInfo->tracking_number)
							@setvar(++$count)
							<tr valign = "top">

								<td align = "left">{{$row->order->short_order}}</td>
								<td align = "left">{{substr($row->order->order_date, 0, 10)}}</td>
								<td align = "center">{{$row->item_quantity}}</td>
								<td align = "left">{{$row->item_code}}</td>
								<td align = "left" rowspan = "2">{{$row->item_description}}</td>
								<td align = "left"
								    rowspan = "2">{!! \Monogram\Helper::jsonTransformer($row->item_option, "<br/>") !!}</td>
								<td align = "left"
								    rowspan = "2">{{ $row->shipInfo ? ($row->shipInfo->tracking_number ? "Yes" : "No" ): "No" }}</td>
							</tr>
							<tr>
								<td colspan = "4" align = "left" valign = "top">
									{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s", $row->id)) !!}
								</td>
							</tr>
						@endif
						@endif


						@endif
					@endforeach
					<tr valign = "top">
						<td colspan = "10">
							<hr size = "1" />
						</td>
					</tr>
					<tr valign = "top">
						<td colspan = "2" align = "right"><strong>Total</strong></td>
						{{--<td><strong>{{\Monogram\Helper::getItemCount($item->groupedItems)}}</strong></td>--}}
						<td><strong>{{ $count }}</strong></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>