<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Users</title>
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
            <li class="active">Users</li>
        </ol>

	    @include('includes.error_div')
	    @include('includes.success_div')

        @if(count($users) > 0)
            <h3 class="page-header">
                Users
                <a class="btn btn-success btn-sm pull-right" href="{{url('/users/create')}}">Create user</a>
            </h3>
            <table class = "table table-bordered">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
                @foreach($users as $user)
                    <tr data-id = "{{$user->id}}">
                        <td>{{ $count++ }}</td>
                        <td>{{ substr($user->username, 0, 30) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles[0]->display_name }}</td>
                        <td>{{ $user->state }}</td>
                        <td>
                            <a href = "{{ url(sprintf("/users/%d", $user->id)) }}" data-toggle = "tooltip"
                               data-placement = "top"
                               title = "View this user"><i class = 'fa fa-eye text-primary'></i></a>
                            | <a href = "{{ url(sprintf("/users/%d/edit", $user->id)) }}" data-toggle = "tooltip"
                                 data-placement = "top"
                                 title = "View this user"><i class = 'fa fa-pencil-square-o text-success'></i></a>
                            | <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
                                 title = "Delete this user"><i class = 'fa fa-times text-danger'></i></a>
                        </td>
                    </tr>
                @endforeach
            </table>
            {!! Form::open(['url' => url('/users/id'), 'method' => 'delete', 'id' => 'delete-user']) !!}
            {!! Form::close() !!}
            <div class = "col-xs-12 text-center">
                {!! $users->render() !!}
            </div>
        @else
            <div class = "col-xs-12">
                <div class = "alert alert-warning text-center">
                    <h3>No user found.</h3>
                </div>
            </div>
        @endif
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
                var form = $("form#delete-user");
                var url = form.attr('action');
                form.attr('action', url.replace('id', id));
                form.submit();
            }
        });
    </script>
</body>
</html>