<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv = "Content-Type" content = "text/html; charset=utf-8" />
	<title>Print a batch</title>
	<link rel = "stylesheet" type = "text/css" href = "{{url("/assets/css/single_batch_print_css.css")}}" />
	<style type = "text/css">
		@page {
			width: 102mm;
			height: 150mm;
			margin-top: 3mm;
			margin-left: 0mm;
			margin-right: 0mm;
		}
		@media print {
			div.current-batch {
				page-break-before: always;
			}
		}
	</style>
</head>
<body>
	@foreach($modules as $module)
		{!! $module !!}
	@endforeach
</body>
</html>
