<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Add order</title>
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
            <li><a href = "{{url('orders/list')}}">Orders</a></li>
            <li class = "active">Add new order</li>
        </ol>
        @if($errors->any())
            <div class = "col-xs-12">
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
            <div class = "col-xs-12">
                <div class = "alert alert-success">
                    {{Session::get('success')}}
                </div>
            </div>
        @endif
        <div class = "col-xs-12">
            {!! Form::open(['method' => 'post', 'url' => url('orders/add'), 'id' => 'search-order', 'class' => 'form-horizontal']) !!}
            <div class = "form-group">
                <label for = "store" class = "col-sm-3 control-label">Market/Store</label>
                <div class = "col-sm-6">
                    {!! Form::select('store', $stores, null, ['id'=>'store', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class = "form-group">
                <label for = "order_ids" class = "col-sm-3 control-label">Order id(s)</label>
                <div class = "col-sm-6">
                    {!! Form::text('order_ids', null, ['id'=>'order_ids', 'class' => 'form-control', 'placeholder' => 'Enter order ids separated by comma']) !!}
                </div>
            </div>
            <div class = "form-group">
                <label for = "" class = "col-sm-3 control-label">Or order</label>
                <div class = "col-sm-6">
                    <div class = "col-xs-6">
                        <div class = "input-group">
                            <span class = "input-group-addon" id = "addon-from">From</span>
                            {!! Form::number('order_from', null, ['id' => 'order_from', 'class' => 'form-control', 'placeholder' => 'From', 'aria-describedby' => 'addon-from']) !!}
                        </div>
                    </div>
                    <div class = "col-xs-6">
                        <div class = "input-group">
                            <span class = "input-group-addon" id = "addon-to">To</span>
                            {!! Form::number('order_to', null, ['id' => 'order_to', 'class' => 'form-control', 'placeholder' => 'To', 'aria-describedby' => 'addon-to']) !!}
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class = "form-group">
                <label for = "to_order_id" class = "col-sm-3 control-label">To Order id</label>
                <div class = "col-sm-1">
                    <input type="checkbox" id="ToOrder" class= 'form-control'/>
                </div>
                <div class = "col-sm-5">
                    {!! Form::text('to_order_id', null, ['id'=>'to_order_id', 'class' => 'form-control' , 'placeholder' => 'To order id ' ,'disabled' => 'disabled'] ) !!}
                </div>
            </div>--}}
            <div class = "form-group">
                <div class = "col-xs-offset-3 col-sm-6">
                    {!! Form::submit('Add order', ['id' => 'add-order', 'class' => 'btn btn-primary']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type = "text/javascript">
        document.getElementById('ToOrder').onchange = function ()
        {
            document.getElementById('to_order_id').disabled = !this.checked;
        }
    </script>
</body>
</html>