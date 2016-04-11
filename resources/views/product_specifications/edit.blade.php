<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Edit Product Spec sheet</title>
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
			<li>Edit Product specifications</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		{!! Form::open(['url' => url('/products_specifications/' . $spec->id), 'method' => 'put', 'files' => true, 'novalidate']) !!}
		<div class = "col-md-12">
			<ul class = "nav nav-tabs" role = "tablist">
				<li role = "presentation" class = "active">
					<a href = "#tab-general" aria-controls = "general" role = "tab"
					   data-toggle = "tab">General info</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-specification" aria-controls = "specification" role = "tab"
					   data-toggle = "tab">Product specification</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-instruction" aria-controls = "instruction" role = "tab"
					   data-toggle = "tab">Product instruction</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-note" aria-controls = "note" role = "tab"
					   data-toggle = "tab">Special Note</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-financial" aria-controls = "financial" role = "tab"
					   data-toggle = "tab">Financial info</a>
				</li>
			</ul>

			<!-- Tab panels -->
			<div class = "tab-content" style = "margin-top: 20px;">
				<div role = "tabpanel" class = "tab-pane fade in active" id = "tab-general">
					@if(json_decode($spec->images))
						<div class = "col-md-12">
							@foreach(json_decode($spec->images) as $image)
								<img src = "{{$image}}" width = "500" height = "500">
							@endforeach
						</div>
					@endif
					<div class = "form-group col-md-12">
						{!! Form::label('product_images', 'Product images', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::file('product_images[]', ['id' => 'product_images', 'multiple' => true, 'accept' => 'image/*',  'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('product_name', 'Product name', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::text('product_name', $spec->product_name, ['id' => 'product_name', 'class' => "form-control"]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('product_sku', 'Product SKU', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::text('product_sku', $spec->product_sku, ['id' => 'product_sku', 'class' => "form-control", 'readonly' => 'readonly']) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('product_description', 'Product description', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::textarea('product_description', $spec->product_description, ['id' => 'product_description', 'rows' => 4, 'class' => "form-control",]) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-specification">
					<div class = "form-group col-md-12">
						{!! Form::label('product_weight', 'Product Weight (lb) :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::number('product_weight', $spec->product_weight, ['id' => 'product_weight', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('product_length', 'Length (in) :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::number('product_length', $spec->product_length, ['id' => 'product_length', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('product_width', 'Width (in) :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::number('product_width', $spec->product_width, ['id' => 'product_width', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('product_height', 'Height (in) :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::number('product_height', $spec->product_height, ['id' => 'product_height', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('packaging_type_name', 'Packaging Type Name :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::text('packaging_type_name', $spec->packaging_type_name, ['id' => 'packaging_type_name', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('packaging_size', 'Packaging size LxWxH (in) :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::text('packaging_size', $spec->packaging_size, ['id' => 'packaging_size', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('packaging_weight', 'Packaging Weight (lb):', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::number('packaging_weight', $spec->packaging_weight, ['id' => 'packaging_weight', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('total_weight', 'Total Weight (lb) :', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::number('total_weight', $spec->total_weight, ['id' => 'total_weight', 'steps' => 'any', 'class' => "form-control",]) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-instruction">
					<div class = "form-group col-md-12">
						{!! Form::label('production_category', 'Production category', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::select('production_category', $production_categories, $spec->production_category_id, ['id' => 'production_category', 'class' => "form-control"]) !!}
						</div>
					</div>
					<div class = "form-group col-md-12">
						{!! Form::label('art_work_location', 'Art work location', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::text('art_work_location', $spec->art_work_location,['id' => 'art-work-location', 'class' => 'form-control']) !!}
						</div>
					</div>
					<div class = "col-md-12">
						<div class = "col-md-6">
							<table class = "table">
								<caption class = "text-center">Production Settings</caption>
								<tbody>
								<tr>
									<td>Temperature</td>
									<td>
										<div class = "form-group">
											{!! Form::text('temperature', $spec->temperature, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								<tr>
									<td>Dwell time</td>
									<td>
										<div class = "form-group">
											{!! Form::text('dwell_time', $spec->dwell_time, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								<tr>
									<td>Pressure</td>
									<td>
										<div class = "form-group">
											{!! Form::text('pressure', $spec->pressure, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								<tr>
									<td>Run Time</td>
									<td>
										<div class = "form-group">
											{!! Form::text('run_time', $spec->run_time, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								</tbody>
							</table>
						</div>

						<div class = "col-md-6">
							<table class = "table">
								<caption class = "text-center">Personalization Details</caption>
								<tbody>
								<tr>
									<td>Type</td>
									<td>
										<div class = "form-group">
											{!! Form::text('type', $spec->type, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								<tr>
									<td>Font</td>
									<td>
										<div class = "form-group">
											{!! Form::text('font', $spec->font, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								<tr>
									<td>Variation name</td>
									<td>
										<div class = "form-group">
											{!! Form::text('variation_name', $spec->variation_name, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								<tr>
									<td>Details</td>
									<td>
										<div class = "form-group">
											{!! Form::text('details', $spec->details, ['class' => 'form-control']) !!}
										</div>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
						<div class = "col-md-12">
							<table class = "table">
								<thead>
								<tr>
									<th>Special Notes for Product Options</th>
									<th>Option Name</th>
									<th>Details</th>
								</tr>
								</thead>
								<tbody id = "product-instruction-table-body">
								@setvar($i = 0)
								@foreach(json_decode($spec->special_note) as $row)
									<tr>
										<td>
											<div class = "form-group">
												{!! Form::text("special_note[$i]", $row[0], ['class' => 'form-control']) !!}
											</div>
										</td>
										<td>
											<div class = "form-group">
												{!! Form::text("option_name[$i]", $row[1], ['class' => 'form-control']) !!}
											</div>
										</td>
										<td>
											<div class = "form-group">
												{!! Form::text("details[$i]", $row[2], ['class' => 'form-control']) !!}
											</div>
										</td>
									</tr>
									@setvar(++$i)
								@endforeach
								</tbody>
								<tfoot>
								<tr>
									<td></td>
									<td></td>
									<td>
										<div class = "form-group">
											{!! Form::button('Add new row', ['id' => 'add-new-instruction-row', 'class' => 'btn btn-success btn-block']) !!}
										</div>
									</td>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-note">
					<div class = "form-group col-md-12">
						{!! Form::label('product_note', 'Product note', ['class' => 'col-md-2 control-label']) !!}
						<div class = "col-md-4">
							{!! Form::textarea('product_note', $spec->product_note, ['id' => 'product_note', 'rows' => 4, 'class' => "form-control",]) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-financial">
					<table class = "table">
						<tr>
							<td>Qty, pcs</td>
							<td>1</td>
							<td>10</td>
							<td>100</td>
							<td>1000</td>
							<td>10000</td>
						</tr>
						<tr>
							<td>Cost, $</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('cost_of_1', $spec->cost_of_1, ['class' => 'form-control']) !!}
								</div>
							</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('cost_of_10', $spec->cost_of_10, ['class' => 'form-control']) !!}
								</div>
							</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('cost_of_100', $spec->cost_of_100, ['class' => 'form-control']) !!}
								</div>
							</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('cost_of_1000', $spec->cost_of_1000, ['class' => 'form-control']) !!}
								</div>
							</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('cost_of_10000', $spec->cost_of_10000, ['class' => 'form-control']) !!}
								</div>
							</td>
						</tr>
					</table>
					<table class = "table" id = "cost-variable-table">
						<thead>
						<tr>
							<th>Parts Name</th>
							<th>Cost Variation 1, $</th>
							<th>Cost Variation 2, $</th>
							<th>Cost Variation 3, $</th>
							<th>Cost Variation 4, $</th>
							<th>Supplier Name</th>
						</tr>
						</thead>
						<tbody id = "cost-variable-table-body">
						@foreach(json_decode($spec->content_cost_info) as $row)
							<tr>
								<td>
									<div class = "form-group">
										{!! Form::text('parts_name[]', $row[0], ['class' => 'form-control']) !!}
									</div>
								</td>
								<td>
									<div class = "form-group">
										{!! Form::text('cost_variation[]', $row[1], ['class' => 'form-control cost-variation-1']) !!}
									</div>
								</td>
								<td>
									<div class = "form-group">
										{!! Form::text('cost_variation[]', $row[2], ['class' => 'form-control cost-variation-2']) !!}
									</div>
								</td>
								<td>
									<div class = "form-group">
										{!! Form::text('cost_variation[]', $row[3], ['class' => 'form-control cost-variation-3']) !!}
									</div>
								</td>
								<td>
									<div class = "form-group">
										{!! Form::text('cost_variation[]', $row[4], ['class' => 'form-control cost-variation-4']) !!}
									</div>
								</td>
								<td>
									<div class = "form-group">
										{!! Form::text('supplier_name[]', $row[5], ['class' => 'form-control']) !!}
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
						<tfoot id = "cost-variable-table-footer">
						<tr>
							<td>Total (SUM), $</td>
							<td class = "cost-variation-1-total">0.0</td>
							<td class = "cost-variation-2-total">0.0</td>
							<td class = "cost-variation-3-total">0.0</td>
							<td class = "cost-variation-4-total">0.0</td>
							<td>
								<div class = "form-group">
									{!! Form::button('Add new row', ['id' => 'add-new-row', 'class' => 'btn btn-success btn-block']) !!}
								</div>
							</td>
						</tr>
						<tr>
							<td>Delivery to us, $</td>
							<td>
								<div class = "form-group">
									{!! Form::text('delivery_cost_variation[]', json_decode($spec->delivery_cost_variation)[0], ['class' => 'form-control cost-variation-1']) !!}
								</div>
							</td>
							<td>
								<div class = "form-group">
									{!! Form::text('delivery_cost_variation[]', json_decode($spec->delivery_cost_variation)[1], ['class' => 'form-control cost-variation-2']) !!}
								</div>
							</td>
							<td>
								<div class = "form-group">
									{!! Form::text('delivery_cost_variation[]', json_decode($spec->delivery_cost_variation)[2], ['class' => 'form-control cost-variation-3']) !!}
								</div>
							</td>
							<td>
								<div class = "form-group">
									{!! Form::text('delivery_cost_variation[]', json_decode($spec->delivery_cost_variation)[3], ['class' => 'form-control cost-variation-4']) !!}
								</div>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>Labor Expense to complete Production Process, $</td>
							<td>
								<div class = "form-group">
									{!! Form::text('labor_expense_cost_variation[]', json_decode($spec->labor_expense_cost_variation)[0], ['class' => 'form-control cost-variation-1']) !!}
								</div>
							</td>
							<td>
								<div class = "form-group">
									{!! Form::text('labor_expense_cost_variation[]', json_decode($spec->labor_expense_cost_variation)[1], ['class' => 'form-control cost-variation-2']) !!}
								</div>
							</td>
							<td>
								<div class = "form-group">
									{!! Form::text('labor_expense_cost_variation[]', json_decode($spec->labor_expense_cost_variation)[2], ['class' => 'form-control cost-variation-3']) !!}
								</div>
							</td>
							<td>
								<div class = "form-group">
									{!! Form::text('labor_expense_cost_variation[]', json_decode($spec->labor_expense_cost_variation)[3], ['class' => 'form-control cost-variation-4']) !!}
								</div>
							</td>
							<td></td>
						</tr>
						</tfoot>
					</table>
					<table class = "table">
						<tr>
							<td>Total Cost Variation 1</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('sum_of_cost_variation_1[]', null, ['class' => 'form-control sum_of_cost_variation_1']) !!}
								</div>
							</td>
						</tr>
						<tr>
							<td>Total Cost Variation 2</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('sum_of_cost_variation_2[]', null, ['class' => 'form-control sum_of_cost_variation_2']) !!}
								</div>
							</td>
						</tr>
						<tr>
							<td>Total Cost Variation 3</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('sum_of_cost_variation_3[]', null, ['class' => 'form-control sum_of_cost_variation_3']) !!}
								</div>
							</td>
						</tr>
						<tr>
							<td>Total Cost Variation 4</td>
							<td>
								<div class = "input-group">
									<span class = "input-group-addon" id = "basic-addon1">$</span>
									{!! Form::text('sum_of_cost_variation_4[]', null, ['class' => 'form-control sum_of_cost_variation_4']) !!}
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-md-2 pull-right">
				{!! Form::submit('Update',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$("#add-new-row").on("click", function (e)
		{
			e.preventDefault();
			var clone = $("#cost-variable-table-body tr:last").clone().find('input').val('').end().insertAfter("#cost-variable-table-body tr:last");
		});

		$("#add-new-instruction-row").on('click', function (e)
		{
			e.preventDefault();
			var clone = $("#product-instruction-table-body tr:last").clone().find('input').val('').end().insertAfter("#product-instruction-table-body tr:last");
		});
		$(document).on('keyup', '#cost-variable-table-body input', function (e)
		{
			e.preventDefault();
			var classes = $(this).attr('class').split(" ");
			if ( classes.length > 1 ) {
				var column_class = classes[1];
				var sum = getTotalCostVariation(column_class);
				/*
				 * CLASS for column: cost-variation-1
				 * CLASS for  total: cost-variation-1-total
				 */
				$("#cost-variable-table-footer td." + column_class + "-total").html(sum);
				setSumOfCostVariation();
			}
		});

		$(document).on('keyup', '#cost-variable-table-footer input', function (e)
		{
			e.preventDefault();
			setSumOfCostVariation();
		});


		function setSumOfCostVariation ()
		{
			var sum = 0.0;
			$(".cost-variation-1").each(function ()
			{
				var parsed = parseFloat($(this).val());
				sum += parsed ? parsed : 0.0;
			});

			$(".sum_of_cost_variation_1").val(sum);

			var sum = 0.0;
			$(".cost-variation-2").each(function ()
			{
				var parsed = parseFloat($(this).val());
				sum += parsed ? parsed : 0.0;
			});

			$(".sum_of_cost_variation_2").val(sum);

			var sum = 0.0;
			$(".cost-variation-3").each(function ()
			{
				var parsed = parseFloat($(this).val());
				sum += parsed ? parsed : 0.0;
			});

			$(".sum_of_cost_variation_3").val(sum);

			var sum = 0.0;
			$(".cost-variation-4").each(function ()
			{
				var parsed = parseFloat($(this).val());
				sum += parsed ? parsed : 0.0;
			});

			$(".sum_of_cost_variation_4").val(sum);
		}

		function getTotalCostVariation (class_name)
		{
			var sum = 0.0;
			$("#cost-variable-table-body input." + class_name).each(function ()
			{
				var parsed = parseFloat($(this).val());
				sum += parsed ? parsed : 0.0;
			});

			return sum.toFixed(2);
		}

		function bootstrap ()
		{
			//var esc = $.Event("keyup", {keyCode: 27});
			//$(nodes).trigger(esc);
			var esc = $.Event("keyup", {keyCode: 27});
			$("#cost-variable-table-body input.cost-variation-1:first").trigger(esc);
			esc = $.Event("keyup", {keyCode: 27});
			$("#cost-variable-table-body input.cost-variation-2:first").trigger(esc);
			esc = $.Event("keyup", {keyCode: 27});
			$("#cost-variable-table-body input.cost-variation-3:first").trigger(esc);
			esc = $.Event("keyup", {keyCode: 27});
			$("#cost-variable-table-body input.cost-variation-4:first").trigger(esc);
		}

		bootstrap();
	</script>
</body>
</html>