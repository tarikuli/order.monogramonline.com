{{--
<div class = "form-group" data-parent="{{$id}}">
	{!! Form::label('category', 'category', ['class' => 'col-xs-2 control-label']) !!}
	<div class = "col-sm-4">
		{!! Form::select('category', $categories, null, ['id' => '', 'class' => "form-control parent-selector", "size" => 12]) !!}
	</div>
</div>
--}}
<div class = "col-sm-3" data-parent="{{$id}}" style="margin-top: 10px;">
	{!! Form::select('category', $categories, null, ['id' => '', 'class' => "form-control parent-selector", "size" => 12]) !!}
</div>