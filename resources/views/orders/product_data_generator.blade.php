<div class = "modal fade {{ $unique_modal_class }}" tabindex = "-1" role = "dialog">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button type = "button" class = "close" data-dismiss = "modal" aria-label = "Close"><span
							aria-hidden = "true">&times;</span></button>
				<h4 class = "modal-title">Add {{ $id_catalog }} to cart</h4>
			</div>
			<div class = "modal-body">
				<table class = "table table-bordered">
					{!! Form::hidden("unique_modal_class[$id_catalog]", $unique_modal_class, ['class' => 'hidden_unique_modal_class']) !!}
					{!! Form::hidden("item_id_catalog[]", $id_catalog, ['class' => 'item_id_catalog']) !!}
					{!! Form::hidden("item_skus[$id_catalog]", $sku, ['class' => 'item_sku']) !!}
					{!! Form::hidden("image[$id_catalog]", $item_image, ['class' => 'item_image']) !!}
					<tr>
						<td>Quantity</td>
						<td>{!! Form::number("item_quantity[$id_catalog]", 0, ['class' => 'form-control']) !!}</td>
					</tr>
					@foreach($crawled_data[$id_catalog] as $node)
						<tr>
							@setvar($label = \Monogram\Helper::specialCharsRemover($node['label']))
							@if($node['type'] == 'text')
								<td>{{ $label }}</td>
								<td>{!! Form::text("item_options[$id_catalog][$label]", null, ['class' => 'form-control']) !!}</td>
							@elseif($node['type'] == 'select')
								<td>{{ $label }}</td>
								<td>
									{!! Form::select("item_descriptions[$id_catalog][$label]", \Monogram\Helper::getOnlyValuesByKey($node['options'], "value"), null, ['class' => 'form-control']) !!}
								</td>
							@endif
						</tr>
					@endforeach
				</table>
			</div>
			<div class = "modal-footer">
				<button type = "button" class = "btn btn-default cancel" data-dismiss = "modal">Cancel</button>
				<button type = "button" class = "btn btn-primary add-item">Add item</button>
			</div>
		</div>
	</div>
</div>