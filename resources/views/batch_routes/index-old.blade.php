<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Batch routes</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link type = "text/css" rel = "stylesheet"
          href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li class="active">Rotes</li>
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
        <div class = "col-xs-12 text-right" style = "margin: 10px 0;">
            <button class = "btn btn-success" type = "button" data-toggle = "collapse" data-target = "#collapsible-top"
                    aria-expanded = "false" aria-controls = "collapsible">Create new batch route
            </button>
            <div class = "collapse text-left" id = "collapsible-top">
                {!! Form::open(['url' => url('/batch_routes'), 'method' => 'post',]) !!}
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_code', 'Batch code', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('batch_code', null, ['id' => 'batch_code', 'class' => "form-control", 'placeholder' => "Enter batch code"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_route_name', 'Route name', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('batch_route_name', null, ['id' => 'batch_route_name', 'class' => "form-control", 'placeholder' => "Enter batch route name"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_max_units', 'Max units', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('batch_max_units', null, ['id' => 'batch_max_units', 'class' => "form-control", 'placeholder' => "Enter batch max units"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_stations', 'Stations', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::select('batch_stations[]', $stations, null, ['id' => 'batch_stations', 'multiple' => true, 'class' => 'selectpicker form-control', "data-live-search" => "true"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_options', 'Options', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::textarea('batch_options', null, ['id' => 'batch_options', 'rows' => 4, 'class' => "form-control", 'placeholder' => "Enter batch options"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    <div class = "col-sm-offset-2 col-sm-4">
                        {!! Form::submit('Create batch route', ['class' => 'btn btn-primary btn-block']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @if(count($batch_routes) > 0)
            <div class = "col-xs-12">
                <table class = "table table-bordered">
                    <tr>
                        <th>#</th>
                        <th>Batch code</th>
                        <th>Route name</th>
                        <th>Max unit</th>
                        <th>Stations</th>
                        <th>Options ( Comma delimited )</th>
                        <th>Action</th>
                    </tr>
                    @foreach($batch_routes as $batch_route)
                        <tr data-id = "{{$batch_route->id}}">
                            <td>{{ $count++ }}</td>
                            <td>{!! Form::text('batch_code', $batch_route->batch_code, ['class' => 'form-control']) !!}</td>
                            <td>{!! Form::text('batch_route_name', $batch_route->batch_route_name, ['class' => 'form-control']) !!}</td>
                            <td>{!! Form::text('batch_max_units', $batch_route->batch_max_units, ['class' => 'form-control']) !!}</td>
                            <td>{!! Form::textarea('batch_stations', implode(",\n", array_map(function($station) { return $station['station_name']; }, $batch_route->stations_list->toArray())), ['class' => 'form-control', 'rows' => 4]) !!}</td>
                            <td>{!! Form::textarea('batch_options', $batch_route->batch_options, ['class' => 'form-control', 'rows' => 4]) !!}</td>
                            <td>
                                <a href = "#" class = "update" data-toggle = "tooltip" data-placement = "top"
                                   title = "Edit this item"><i class = "fa fa-pencil-square-o text-success"></i></a>
                                | <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
                                     title = "Delete this item"> <i class = "fa fa-times text-danger"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class = "col-xs-12 text-center">
                {!! $batch_routes->render() !!}
            </div>
            {!! Form::open(['url' => url('/batch_routes/id'), 'method' => 'delete', 'id' => 'delete-batch-route']) !!}
            {!! Form::close() !!}

            {!! Form::open(['url' => url('/batch_routes/id'), 'method' => 'put', 'id' => 'update-batch-routes']) !!}
            {!! Form::hidden('batch_code', null, ['id' => 'update_batch_code']) !!}
            {!! Form::hidden('batch_route_name', null, ['id' => 'update_batch_route_name']) !!}
            {!! Form::hidden('batch_max_units', null, ['id' => 'update_batch_max_units']) !!}
            {!! Form::hidden('batch_stations', null, ['id' => 'update_batch_stations']) !!}
            {!! Form::hidden('batch_options', null, ['id' => 'update_batch_options']) !!}
            {!! Form::close() !!}

        @else
            <div class = "col-xs-12">
                <div class = "alert alert-warning text-center">
                    <h3>No batch route found.</h3>
                </div>
            </div>
        @endif

        <div class = "col-xs-12 text-right" style = "margin: 10px 0;">
            <div class = "collapse text-left" id = "collapsible-bottom">
                {!! Form::open(['url' => url('/batch_routes'), 'method' => 'post',]) !!}
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_code', 'Batch code', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('batch_code', null, ['id' => 'batch_code', 'class' => "form-control", 'placeholder' => "Enter batch code"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_route_name', 'Route name', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('batch_route_name', null, ['id' => 'batch_route_name', 'class' => "form-control", 'placeholder' => "Enter batch route name"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_max_units', 'Max units', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('batch_max_units', null, ['id' => 'batch_max_units', 'class' => "form-control", 'placeholder' => "Enter batch max units"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_stations', 'Stations', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::select('batch_stations[]', $stations, null, ['id' => 'batch_stations', 'multiple' => true, 'class' => 'selectpicker form-control', "data-live-search" => "true"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('batch_options', 'Options', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::textarea('batch_options', null, ['id' => 'batch_options', 'rows' => 4, 'class' => "form-control", 'placeholder' => "Enter batch options"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    <div class = "col-sm-offset-2 col-sm-4">
                        {!! Form::submit('Create batch route', ['class' => 'btn btn-primary btn-block']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <button class = "btn btn-success" type = "button" data-toggle = "collapse"
                    data-target = "#collapsible-bottom"
                    aria-expanded = "false" aria-controls = "collapsible">Create new batch route
            </button>
        </div>

    </div>
    <script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type = "text/javascript"
            src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/js/bootstrap-select.min.js"></script>
    <script type = "text/javascript">
        $(function ()
        {
            $('[data-toggle="tooltip"]').tooltip();
        });
        var message = {
            delete: 'Are you sure you want to delete?',
        };
        $("a.delete").on('click', function (event)
        {
            event.preventDefault();
            var id = $(this).closest('tr').attr('data-id');
            var action = confirm(message.delete);
            if ( action ) {
                var form = $("form#delete-batch-route");
                var url = form.attr('action');
                form.attr('action', url.replace('id', id));
                form.submit();
            }
        });

        $("a.update").on('click', function (event)
        {
            event.preventDefault();
            var tr = $(this).closest('tr');
            var id = tr.attr('data-id');

            code = tr.find('input').eq(0).val();
            route = tr.find('input').eq(1).val();
            unit = tr.find('input').eq(2).val();
            stations = tr.find('textarea').eq(0).val();
            options = tr.find('textarea').eq(1).val();

            $("input#update_batch_code").val(code);
            $("input#update_batch_route_name").val(route);
            $("input#update_batch_max_units").val(unit);
            $("input#update_batch_stations").val(stations);
            $("input#update_batch_options").val(options);

            var form = $("form#update-batch-routes");
            var url = form.attr('action');
            form.attr('action', url.replace('id', id));
            form.submit();
        });
    </script>
</body>
</html>