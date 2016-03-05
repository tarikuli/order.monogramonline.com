<div class="current-batch">
	<table width = "100%" cellpadding = "2" cellspacing = "2" border = "0">
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
				<table width = "100%" cellpadding = "2" cellspacing = "2" border = "0">
					<tr valign = "top">
						<th align = "left">Order</th>
						<th align = "left">Date</th>
						<th align = "left">Qty</th>
						<th align = "left">SKU</th>
						<th align = "left">Item name</th>
						<th align = "left">Options</th>
						<th align = "left">Shipped ?</th>
					</tr>
					@foreach($item->groupedItems as $row)
						<tr valign = "top">
							<td align = "left">{{$row->order_id}}</td>
							<td align = "left">{{substr($row->order->order_date, 0, 10)}}</td>
							<td align = "left">{{$row->item_quantity}}</td>
							<td align = "left">{{$row->item_code}}</td>
							<td align = "left">{{$row->item_description}}</td>
							<td align = "left">{!! \Monogram\Helper::jsonTransformer($item->item_option, "<br/>") !!}</td>
							<td align = "left">{{ $row->shipInfo ? ($row->shipInfo->tracking_number ? "Yes" : "No" ): "No" }}</td>
						</tr>
					@endforeach
					<tr valign = "top">
						<td colspan = "10">
							<hr size = "1" />
						</td>
					</tr>
					<tr valign = "top">
						<td colspan = "2" align = "right"><strong>Total</strong></td>
						<td><strong>{{\Monogram\Helper::getItemCount($item->groupedItems)}}</strong></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>