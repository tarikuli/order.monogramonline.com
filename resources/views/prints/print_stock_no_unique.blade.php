<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv = "Content-Type" content = "text/html; charset=utf-8" />
	<title>Print a batch</title>
	<link rel = "stylesheet" type = "text/css" href = "{{url("/assets/css/single_batch_print_css.css")}}" />
	<style type = "text/css">
		@page {
			width: 5.5cm;
			height: 2.6mm;
			margin-top: 1cm;
			margin-left: 0cm;
			margin-right: 0cm;
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 10px;
		}
		@media print {
			div.current-batch {
				page-break-before: always;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 10px;
				border-style: solid;
				border-size: 1px;
				border-color: black,;
				overflow: hidden;
			}
		}
		div.current-batch {
				page-break-before: always;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				border-style: dotted;
				border-size: 0px;
				border-color: black,;
 				overflow: hidden;
		}
	</style>
</head>
<body>
<div class = "current-batch" style="width: 5.3cm; height: 2.5cm;">

<table cellpadding = "0" cellspacing = "0" width = "100%" border = "0">
	<tr valign = "top">
		<td>Stock#{{ $inventory->stock_no_unique }}</td>
		<td rowspan="3">
			<img style="height:1.5cm; width: auto; overflow: hidden;" 
												     src = "{{$inventory->warehouse}}"
												     border = "0" />
		</td>		
	</tr>
	<tr valign = "top">
		<td colspan = "1" align = "left">
			{!! \Monogram\Helper::getHtmlBarcode($inventory->stock_no_unique, 1) !!}
		</td>
	</tr>
	<tr>
		<td colspan = "1" align = "left" >
			Bin#{{ $inventory->wh_bin }}
		</td>
	</tr>
	<tr>
		<td colspan = "2" align = "left" >
			{{ $inventory->stock_name_discription }}
		</td>
	</tr>

</table>

<div>

</body>
</html>
