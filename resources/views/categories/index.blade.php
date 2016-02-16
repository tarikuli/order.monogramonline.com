<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Sub category 1</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li class="active">Categories</li>
        </ol>
	    @include('includes.error_div')
	    @include('includes.success_div')
        <div class = "col-xs-12 text-right" style = "margin: 10px 0;">
            <button class = "btn btn-success" type = "button" data-toggle = "collapse" data-target = "#collapsible-top"
                    aria-expanded = "false" aria-controls = "collapsible">Create sub category 1
            </button>
            <div class = "collapse text-left" id = "collapsible-top">
                {!! Form::open(['url' => url('/categories'), 'method' => 'post']) !!}
                <div class = "form-group col-xs-12">
                    {!! Form::label('category_code', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('category_code', null, ['id' => 'category_code', 'class' => "form-control", 'placeholder' => "Enter category code"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('category_description', 'Description', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('category_description', null, ['id' => 'category_description', 'class' => "form-control", 'placeholder' => "Enter category description"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('category_display_order', 'Display order', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('category_display_order', 0, ['id' => 'category_display_order', 'class' => "form-control", 'placeholder' => "Enter category display order"]) !!}
                    </div>
                </div>
                <div class = "col-xs-12 apply-margin-top-bottom">
                    <div class = "col-xs-offset-2 col-xs-4">
                        {!! Form::submit('Create sub category', ['class' => 'btn btn-primary btn-block']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @if(count($categories) > 0)
            <div class = "col-xs-12">
                <table class = "table table-bordered">
                    <tr>
                        <th>#</th>
                        <th>category_code</th>
                        <th>category_description</th>
                        <th>category_display_order</th>
                        <th>Action</th>
                    </tr>
                    @foreach($categories as $category)
                        <tr data-id = "{{$category->id}}">
                            <td>{{ $count++ }}</td>
	                        <td>
		                        <input class = "form-control" name = "category_code" type = "text"
		                               value = "{{$category->category_code}}">
	                        </td>
	                        <td>
		                        <input class = "form-control" name = "category_description" type = "text"
		                               value = "{{$category->category_description}}">
	                        </td>
	                        <td>
		                        <input class = "form-control" name = "category_display_order" type = "text"
		                               value = "{{$category->category_display_order}}">
	                        </td>
                            {{--<td>{!! Form::text('category_code', $category->category_code, ['class' => 'form-control']) !!}</td>
                            <td>{!! Form::text('category_description', $category->category_description, ['class' => 'form-control']) !!}</td>
                            <td>{!! Form::text('category_display_order', $category->category_display_order, ['class' => 'form-control']) !!}</td>--}}
                            <td>
                                {{--<a href = "{{ url(sprintf("/categories/%d", $category->id)) }}" class = "btn btn-success">View</a> | --}}
                                <a href = "#" class = "update" data-toggle = "tooltip" data-placement = "top"
                                   title = "Edit this item"><i class = "fa fa-pencil-square-o text-success"></i> </a>
                                | <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
                                     title = "Delete this item"><i class = "fa fa-times text-danger"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-xs-12 text-center">
                {!! $categories->render() !!}
            </div>
            {!! Form::open(['url' => url('/categories/id'), 'method' => 'delete', 'id' => 'delete-category']) !!}
            {!! Form::close() !!}

            {!! Form::open(['url' => url('/categories/id'), 'method' => 'put', 'id' => 'update-category']) !!}
            {!! Form::hidden('category_code', null, ['id' => 'update_category_code']) !!}
            {!! Form::hidden('category_description', null, ['id' => 'update_category_description']) !!}
            {!! Form::hidden('category_display_order', null, ['id' => 'update_category_display_order']) !!}
            {!! Form::close() !!}
        @else
            <div class = "col-xs-12">
                <div class = "alert alert-warning text-center">
                    <h3>No category found.</h3>
                </div>
            </div>
        @endif
        <div class = "col-xs-12 text-right" style = "margin: 10px 0;">
            <button class = "btn btn-success" type = "button" data-toggle = "collapse"
                    data-target = "#collapsible-bottom"
                    aria-expanded = "false" aria-controls = "collapsible">Create sub category 1
            </button>
            <div class = "collapse text-left" id = "collapsible-bottom">
                {!! Form::open(['url' => url('/categories'), 'method' => 'post']) !!}
                <div class = "form-group col-xs-12">
                    {!! Form::label('category_code', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('category_code', null, ['id' => 'category_code', 'class' => "form-control", 'placeholder' => "Enter category code"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('category_description', 'Description', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('category_description', null, ['id' => 'category_description', 'class' => "form-control", 'placeholder' => "Enter category description"]) !!}
                    </div>
                </div>
                <div class = "form-group col-xs-12">
                    {!! Form::label('category_display_order', 'Display order', ['class' => 'col-xs-2 control-label']) !!}
                    <div class = "col-sm-4">
                        {!! Form::text('category_display_order', 0, ['id' => 'category_display_order', 'class' => "form-control", 'placeholder' => "Enter category display order"]) !!}
                    </div>
                </div>
                <div class = "col-xs-12 apply-margin-top-bottom">
                    <div class = "col-xs-offset-2 col-xs-4">
                        {!! Form::submit('Create sub category', ['class' => 'btn btn-primary btn-block']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
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
                var form = $("form#delete-category");
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

            var code = tr.find('input').eq(0).val();
            var description = tr.find('input').eq(1).val();
            var order = tr.find('input').eq(2).val();

            $("input#update_category_code").val(code);
            $("input#update_category_description").val(description);
            $("input#update_category_display_order").val(order);
            var form = $("form#update-category");
            var url = form.attr('action');
            form.attr('action', url.replace('id', id));
            form.submit();
        });
    </script>
</body>
</html>