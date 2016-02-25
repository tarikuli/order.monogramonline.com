<div class = "form-group col-xs-12" data-parent="{{$id}}">
	{!! Form::label('category', 'category', ['class' => 'col-xs-2 control-label']) !!}
	<div class = "col-sm-4">
		{!! Form::select('category', $categories, null, ['id' => '', 'class' => "form-control parent-selector"]) !!}
	</div>
</div>