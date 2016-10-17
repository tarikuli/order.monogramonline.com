<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv = "Content-Type" content = "text/html; charset=utf-8" />
	<title>Print a batch</title>
	<link rel = "stylesheet" type = "text/css" href = "{{url("/assets/css/single_batch_print_css.css")}}" />
	<style type = "text/css">
	
	html, body {
	    /*changing width to 100% causes huge overflow and wrap*/
	    height:101mm;
	    width:152mm; 
	    overflow: hidden;
	    background: #FFF; 
	   font-size: 10px;
	  }
  
		@page {
			padding:0mm;
			margin:0mm;
			width:150mm;
			height:100mm;
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 10px;
			border: 1px solid red;
		}
		@media print {
		 * { margin: 0 !important; padding: 0 !important; }
			div.current-batch {
				page-break-before: always;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 10px;
			
			}
		}
	</style>
	<script>
		window.print();
	</script>
</head>
<body style="border: 1px solid red">
	<div class="current-batch" style="width:150mm; height: 100mm; border: 1px solid green; "> 
		<img style="width:175mm; height: auto; overflow: hidden;"  src="data:image/gif;base64,{{ $labelImage}} "/>
	</div>
</body>
</html>
