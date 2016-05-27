<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Add order manually</title>
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
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('orders/list')}}">Orders</a></li>
			<li class = "active">Add new order manually</li>
		</ol>
		@if($errors->any())
			<div class = "col-md-12">
				<div class = "alert alert-danger">
					<ul>
						@foreach($errors->all() as $error)
							<li>{{$error}}</li>
						@endforeach
					</ul>
				</div>
			</div>
		@endif
		@if(Session::has('success'))
			<div class = "col-md-12">
				<div class = "alert alert-success">
					{{Session::get('success')}}
				</div>
			</div>
		@endif
		{!! Form::open(['method' => 'post', 'url' => url('orders/manual'), 'id' => 'manual-order-placement', 'class' => 'form-horizontal']) !!}
		<div class = "row">
			<div class = "col-md-10">
				<div class = "form-group">
					<label for = "store" class = "col-md-2 control-label">Market/Store</label>
					<div class = "col-md-4">
						{!! Form::select('store', $stores, null, ['id'=>'store', 'class' => 'form-control']) !!}
					</div>
				</div>
				<div class = "row">
					<div class = "col-md-offset-2 col-md-10">
						<table>
							<tr>
								<td style = "font-weight: bold;color: #686869;padding-top:15px">Ship To:</td>
								<td style = "font-weight: bold;color: #686869;padding-left:487px;padding-top:15px">Bill To:</td>
							</tr>
						</table>
						<table>
							<tr>
								<td>Full Name</td>
								<td>{!! Form::text('ship_full_name', null, ['id' => 'ship_full_name']) !!}</td>
								<td></td>
								<!---->
								<td style = "padding-left:97px">Full Name</td>
								<td>{!! Form::text('bill_full_name', null, ['id' => 'bill_full_name']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>Company Name</td>
								<td>{!! Form::text('ship_company_name', null, ['id' => 'company_name']) !!}</td>
								<td></td>
								<!---->
								<td style = "padding-left:97px">Company Name</td>
								<td>{!! Form::text('bill_company_name', null, ['id' => 'bill_company_name']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>First/last Name</td>
								<td>{!! Form::text('ship_first_name', null, ['id' => 'ship_first_name']) !!}</td>
								<td>{!! Form::text('ship_last_name', null, ['id' => 'ship_last_name']) !!}</td>
								<td style = "padding-left:97px">First/last Name</td>
								<td>{!! Form::text('bill_first_name', null, ['id' => 'bill_first_name', 'class' => '']) !!}</td>
								<td>{!! Form::text('bill_last_name', null, ['id' => 'bill_last_name', 'class' => '']) !!}</td>
							</tr>
							<tr>
								<td>Addr1</td>
								<td>{!! Form::text('ship_address_1', null, ['id' => 'ship_address_1']) !!}</td>
								<td></td>
								<td style = "padding-left:97px">Addr1</td>
								<td>{!! Form::text('bill_address_1', null, ['id' => 'bill_address_1', 'class' => '']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>Addr2</td>
								<td>{!! Form::text('ship_address_2', null, ['id' => 'ship_address_2']) !!}</td>
								<td></td>
								<td style = "padding-left:97px">Addr2</td>
								<td>{!! Form::text('bill_address_2', null, ['id' => 'bill_address_2']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>City, State, Zip</td>
								<td>{!! Form::text('ship_city', null, ['id' => 'ship_city']) !!}</td>
								<td>{!! Form::text('ship_state', null, ['id' => 'ship_state']) !!}</td>
								<td style = "padding-left:97px">City, State, Zip</td>
								<td>{!! Form::text('bill_city', null, ['id' => 'bill_city']) !!}</td>
								<td>{!! Form::text('bill_state', null, ['id' => 'bill_state']) !!}</td>
							</tr>
							<tr>
								<td></td>
								<td>{!! Form::text('ship_zip', null, ['id' => 'ship_zip','style'=>'width: 100px']) !!}</td>
								<td></td>
								<td style = "padding-left:97px"></td>
								<td>{!! Form::text('bill_zip', null, ['id' => 'bill_zip','style'=>'width: 100px']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>Country</td>
								<td>{!! Form::text('ship_country', null, ['id' => 'ship_country']) !!}</td>
								<td></td>
								<td style = "padding-left:97px">Country</td>
								<td>{!! Form::text('bill_country', null, ['id' => 'bill_country']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>Phone</td>
								<td>{!! Form::text('ship_phone', null, ['id' => 'company_name']) !!}</td>
								<td></td>
								<td style = "padding-left:97px">Phone</td>
								<td>{!! Form::text('bill_phone', null, ['id' => 'bill_phone']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>Email</td>
								<td>{!! Form::text('ship_email', null, ['id' => 'ship_email']) !!}</td>
								<td></td>
								<td style = "padding-left:97px">Email</td>
								<td>{!! Form::text('bill_email', null, ['id' => 'bill_email']) !!}</td>
								<td></td>
							</tr>
							<tr>
								<td>Shipping</td>
								<td>{!! Form::text('shipping', null, ['id' => 'ship_email']) !!}</td>
								<td></td>
								<td style = "padding-left:97px">Bill mailing list</td>
								<td>{!! Form::text('bill_mailing_list', null, ['id' => 'bill_mailing_list']) !!}</td>
								<td></td>
							</tr>
						</table>
						<table>
							<tr>
								<td>
									<hr style = "width: 470px; color: black; background-color:black;margin-top: 10px"
									    size = "1" />
								</td>
								<td>
									<hr style = "margin-left:58px;width: 470px; color: black; background-color:black;margin-top:  10px"
									    size = "1" />
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class = "row">
					<div class = "col-md-12">
						<table class = "table table-bordered" id = "selected-items">
							<caption>Selected items</caption>
							<tr>
								<td>Image</td>
								<td>Id catalog</td>
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
								<button class = "btn btn-default" type = "button" id = "puller">Pull</button>
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
			<div class = "row">
				<div class = "col-md-12">
					<table style = "margin-left:775px">
						<tr>
							<td align = "right" style = "padding-right:40px ">Subtotal:</td>
							<td align = "right">{!! Form::text('subtotal', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Coupon-discount</b>:</td>
							<td align = "right">{!! Form::text('coupon_id', null) !!} - {!! Form::text('coupon_value', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Gift Wrap:</td>
							<td align = "right">{!! Form::text('gift_wrap_cost', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Shipping:</td>
							<td align = "right">{!! Form::text('shipping_charge', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:40px ">Insurance:</td>
							<td align = "right">{!! Form::text('insurance', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:45px ">Adjustments:</td>
							<td align = "right">{!! Form::text('adjustments', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:45px ">Tax:</td>
							<td align = "right">{!! Form::text('tax_charge', null) !!}</td>
						</tr>
						<tr>
							<td align = "right" style = "padding-right:45px ">Total:</td>
							<td align = "right">{!! Form::text('total', null) !!}</td>
						</tr>
						<tr>

						</tr>
					</table>
				</div>
			</div>
			<div class = "row" id = "items-holder">
			</div>
			<div class = "form-group">
				<div class = "col-md-offset-1 col-md-4">
					{!! Form::submit('Add order', ['id' => 'add-order', 'class' => 'btn btn-primary btn-sm']) !!}
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript" src = "/assets/js/nprogress.js"></script>
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
				var node = "<tr data-id-catalog='" + value.id_catalog + "' data-sku = '" + value.product_model + "'>" + "<td><img width='50' height='50' src='" + value.product_thumb + "'</td>" + "<td>" + value.product_name + "</td>" + "<td>" + value.product_model + "</td>" + "<td>" + value.id_catalog + "</td>" + "</tr>";
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
			var sku = $(this).attr('data-sku');
			var url = "/orders/product_info";
			var data = {
				"id_catalog": id_catalog, "sku": sku
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
			alert("Something went wrong!");
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
				var modal_class = body.find('.hidden_unique_modal_class').val();
				var item_image = body.find(".item_image").val();
				var item_id_catalog = body.find(".item_id_catalog").val();
				var tr = "<tr data-modal-class='" + modal_class + "'>" + "<td> <img src='" + item_image + "' width='50' height='50' /> </td>" + "<td>" + item_id_catalog + "</td>" + "<td>" + quantity + "</td>" + "<td>" + "<a href='#' class='delete-row'>Delete</a>" + "</td>";
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
				$("." + modal_class).find('.modal-body').remove();
				tr.remove();
			}
		})
	</script>
</body>
</html>